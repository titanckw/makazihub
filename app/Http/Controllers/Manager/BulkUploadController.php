<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class BulkUploadController extends Controller
{
    /**
     * Show the bulk upload landing page.
     */
    public function index()
    {
        return view('manager.bulk-upload.index');
    }

    // -------------------------------------------------------
    // TENANTS
    // -------------------------------------------------------

    /**
     * Show the tenant bulk upload form.
     */
    public function tenantsForm()
    {
        return view('manager.bulk-upload.tenants');
    }

    /**
     * Download the tenant CSV template.
     */
    public function downloadTenantTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tenant_upload_template.csv"',
        ];

        $columns = [
            'name',
            'email',
            'phone',
            'id_number',
            'emergency_contact_name',
            'emergency_contact_phone',
            'occupation',
            'employer',
            'notes',
        ];

        $example = [
            'John Mwangi',
            'john.mwangi@email.com',
            '0712345678',
            '12345678',
            'Jane Mwangi',
            '0723456789',
            'Software Engineer',
            'Safaricom PLC',
            'Referred by existing tenant',
        ];

        $callback = function () use ($columns, $example) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, $example);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process and import tenant CSV/Excel file.
     */
    public function importTenants(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $rows = $this->parseFile($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'The uploaded file appears to be empty or could not be read.');
        }

        // Normalise headers
        $headers = array_map(fn($h) => strtolower(trim(str_replace([' ', '-'], '_', $h))), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $required = ['name', 'email', 'phone', 'id_number'];
        $missing  = array_diff($required, $headers);

        if (!empty($missing)) {
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing) . '. Please use the provided template.');
        }

        $manager = Auth::user();
        $tenantRole = Role::findByName('tenant');

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($dataRows as $index => $row) {
            $lineNum = $index + 2; // +2: header is row 1, data starts row 2

            // Skip blank rows
            if (empty(array_filter($row))) {
                continue;
            }

            $data = array_combine($headers, array_pad($row, count($headers), null));

            $validator = Validator::make($data, [
                'name'       => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email',
                'phone'      => 'required|string|max:20',
                'id_number'  => 'required|string|max:50|unique:tenants,id_number',
            ]);

            if ($validator->fails()) {
                $skipped++;
                $errors[] = "Row {$lineNum} ({$data['name']}): " . implode('; ', $validator->errors()->all());
                continue;
            }

            try {
                DB::transaction(function () use ($data, $manager, $tenantRole) {
                    $user = User::create([
                        'name'     => trim($data['name']),
                        'email'    => strtolower(trim($data['email'])),
                        'phone'    => trim($data['phone'] ?? ''),
                        'password' => Hash::make('Tenant@1234'),
                    ]);

                    $user->assignRole($tenantRole);

                    Tenant::create([
                        'user_id'                 => $user->id,
                        'manager_id'              => $manager->id,
                        'id_number'               => trim($data['id_number']),
                        'emergency_contact_name'  => trim($data['emergency_contact_name'] ?? ''),
                        'emergency_contact_phone' => trim($data['emergency_contact_phone'] ?? ''),
                        'occupation'              => trim($data['occupation'] ?? ''),
                        'employer'                => trim($data['employer'] ?? ''),
                        'notes'                   => trim($data['notes'] ?? ''),
                        'status'                  => 'active',
                    ]);
                });

                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "Row {$lineNum} ({$data['name']}): Unexpected error — {$e->getMessage()}";
            }
        }

        $message = "{$imported} tenant(s) imported successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped.";
        }

        $flashKey = $skipped > 0 ? 'warning' : 'success';

        return redirect()
            ->route('manager.tenants.index')
            ->with($flashKey, $message)
            ->with('import_errors', $errors);
    }

    // -------------------------------------------------------
    // PROPERTIES (with units)
    // -------------------------------------------------------

    /**
     * Show the property bulk upload form.
     */
    public function propertiesForm()
    {
        return view('manager.bulk-upload.properties');
    }

    /**
     * Download the properties CSV template.
     */
    public function downloadPropertyTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="property_upload_template.csv"',
        ];

        $columns = [
            // Property fields
            'property_name',
            'address',
            'city',
            'county',
            'property_type',   // apartment | maisonette | commercial | bedsitter | townhouse
            'description',
            // Unit fields (one row per unit; property fields repeat)
            'unit_number',
            'unit_type',       // 1br | 2br | 3br | studio | bedsitter | commercial
            'floor',
            'rent_amount',
            'deposit_amount',
        ];

        $examples = [
            [
                'Sunrise Apartments', 'Thika Road, Kasarani', 'Nairobi', 'Nairobi',
                'apartment', 'Modern 2-bedroom apartments near Garden City',
                'A01', '2br', '2', '25000', '50000',
            ],
            [
                'Sunrise Apartments', 'Thika Road, Kasarani', 'Nairobi', 'Nairobi',
                'apartment', 'Modern 2-bedroom apartments near Garden City',
                'A02', '2br', '2', '25000', '50000',
            ],
            [
                'Sunrise Apartments', 'Thika Road, Kasarani', 'Nairobi', 'Nairobi',
                'apartment', '',
                'B01', 'bedsitter', '1', '12000', '24000',
            ],
        ];

        $callback = function () use ($columns, $examples) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($examples as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process and import properties CSV/Excel file.
     *
     * Each CSV row represents one unit. Rows sharing the same property_name
     * (case-insensitive) under the same manager are grouped into one property.
     */
    public function importProperties(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $rows = $this->parseFile($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'The uploaded file appears to be empty or could not be read.');
        }

        $headers  = array_map(fn($h) => strtolower(trim(str_replace([' ', '-'], '_', $h))), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $required = ['property_name', 'address', 'city', 'county', 'property_type', 'unit_number', 'rent_amount'];
        $missing  = array_diff($required, $headers);

        if (!empty($missing)) {
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing) . '. Please use the provided template.');
        }

        $manager = Auth::user();

        // Group rows by property_name so we create each property once
        $grouped = [];
        foreach ($dataRows as $index => $row) {
            if (empty(array_filter($row))) {
                continue;
            }
            $data = array_combine($headers, array_pad($row, count($headers), null));
            $key  = strtolower(trim($data['property_name'] ?? ''));
            if ($key) {
                $grouped[$key][] = ['line' => $index + 2, 'data' => $data];
            }
        }

        $propertiesCreated = 0;
        $unitsCreated      = 0;
        $skipped           = 0;
        $errors            = [];

        $validTypes     = ['apartment', 'maisonette', 'commercial', 'bedsitter', 'townhouse'];
        $validUnitTypes = ['1br', '2br', '3br', 'studio', 'bedsitter', 'commercial'];

        foreach ($grouped as $propKey => $unitRows) {
            // Use the first row to define property-level fields
            $first    = $unitRows[0]['data'];
            $propType = strtolower(trim($first['property_type'] ?? ''));

            if (!in_array($propType, $validTypes)) {
                foreach ($unitRows as $ur) {
                    $errors[] = "Row {$ur['line']}: Invalid property_type '{$propType}'. Must be one of: " . implode(', ', $validTypes);
                    $skipped++;
                }
                continue;
            }

            // Find or create the property (idempotent — skip if already exists for this manager)
            $property = Property::firstOrCreate(
                [
                    'manager_id' => $manager->id,
                    'name'       => trim($first['property_name']),
                ],
                [
                    'address'       => trim($first['address']),
                    'city'          => trim($first['city']),
                    'county'        => trim($first['county']),
                    'property_type' => $propType,
                    'description'   => trim($first['description'] ?? ''),
                    'is_active'     => true,
                ]
            );

            if ($property->wasRecentlyCreated) {
                $propertiesCreated++;
            }

            // Create units for this property
            foreach ($unitRows as $ur) {
                $d          = $ur['data'];
                $unitNumber = trim($d['unit_number'] ?? '');
                $lineNum    = $ur['line'];

                if (!$unitNumber) {
                    $errors[] = "Row {$lineNum}: unit_number is required.";
                    $skipped++;
                    continue;
                }

                // Skip duplicate unit numbers within the same property
                $exists = Unit::where('property_id', $property->id)
                    ->where('unit_number', $unitNumber)
                    ->exists();

                if ($exists) {
                    $errors[] = "Row {$lineNum}: Unit '{$unitNumber}' already exists in '{$property->name}'.";
                    $skipped++;
                    continue;
                }

                $unitType = strtolower(trim($d['unit_type'] ?? 'apartment'));
                if (!in_array($unitType, $validUnitTypes)) {
                    $unitType = 'apartment'; // safe fallback
                }

                $validator = Validator::make($d, [
                    'rent_amount' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row {$lineNum}: " . implode('; ', $validator->errors()->all());
                    $skipped++;
                    continue;
                }

                try {
                    Unit::create([
                        'property_id'    => $property->id,
                        'unit_number'    => $unitNumber,
                        'unit_type'      => $unitType,
                        'floor'          => (int) ($d['floor'] ?? 0),
                        'rent_amount'    => (float) $d['rent_amount'],
                        'deposit_amount' => (float) ($d['deposit_amount'] ?? ($d['rent_amount'] * 2)),
                        'status'         => 'vacant',
                        'description'    => trim($d['description'] ?? ''),
                    ]);

                    $unitsCreated++;
                } catch (\Throwable $e) {
                    $errors[] = "Row {$lineNum} (Unit {$unitNumber}): {$e->getMessage()}";
                    $skipped++;
                }
            }
        }

        $message = "{$propertiesCreated} property(ies) and {$unitsCreated} unit(s) created.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped.";
        }

        $flashKey = $skipped > 0 ? 'warning' : 'success';

        return redirect()
            ->route('manager.properties.index')
            ->with($flashKey, $message)
            ->with('import_errors', $errors);
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /**
     * Parse an uploaded CSV or Excel file into a 2D array.
     * Returns an empty array on failure.
     */
    private function parseFile(\Illuminate\Http\UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['xlsx', 'xls'])) {
            return $this->parseExcel($file->getRealPath());
        }

        return $this->parseCsv($file->getRealPath());
    }

    private function parseCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        // Detect and skip BOM (UTF-8 with BOM from Excel)
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    private function parseExcel(string $path): array
    {
        // Use PhpSpreadsheet if available; fall back to reading as CSV otherwise
        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $sheet       = $spreadsheet->getActiveSheet();
                $rows        = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getFormattedValue();
                    }
                    // Trim trailing empty cells but keep the row if it has any content
                    $trimmed = rtrim(implode('', $cells));
                    if ($trimmed !== '') {
                        $rows[] = $cells;
                    }
                }

                return $rows;
            } catch (\Throwable $e) {
                // Fall through to CSV parse
            }
        }

        // PhpSpreadsheet not available — try parsing xlsx as CSV (last resort)
        return $this->parseCsv($path);
    }
}
