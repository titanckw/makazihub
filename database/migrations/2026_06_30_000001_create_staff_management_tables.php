<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- Profile: avatar on users ----------
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar_path');
            }
        });

        // ---------- Attendance ----------
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->decimal('clock_in_lat', 10, 7)->nullable();
            $table->decimal('clock_in_lng', 10, 7)->nullable();
            $table->decimal('clock_out_lat', 10, 7)->nullable();
            $table->decimal('clock_out_lng', 10, 7)->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'on_leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'date']);
            $table->index('date');
        });

        // ---------- Leave Management ----------
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('allocated_days')->default(21);
            $table->unsignedSmallInteger('used_days')->default(0);
            $table->timestamps();

            $table->unique(['staff_id', 'year']);
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('type')->default('annual'); // annual, sick, unpaid, compassionate
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('days');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('manager_comment')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'status']);
        });

        // ---------- Chat ----------
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'recipient_id']);
            $table->index(['recipient_id', 'read_at']);
        });

        // ---------- Document Storage ----------
        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('category')->default('other'); // cv, id, contract, certificate, other
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_documents');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('attendance_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'bio']);
        });
    }
};
