{{-- resources/views/shared/chat-panel.blade.php
     Shared chat UI used by both staff.chat.index and manager.chat.index.
     Polls every 3s for new messages — no websocket server required. --}}
<div class="bg-white rounded-2xl border border-border overflow-hidden flex h-[calc(100vh-220px)]"
     x-data="chatPanel({
        partners: {{ $partners->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'avatar' => $p->avatar_url])->values()->toJson() }},
        activeId: {{ $activePartner->id ?? 'null' }},
        unread: {{ $unreadCounts->toJson() }},
        pollUrl: '{{ route($routePrefix . '.chat.poll') }}',
        storeUrl: '{{ route($routePrefix . '.chat.store') }}',
        indexUrl: '{{ route($routePrefix . '.chat.index') }}',
        meId: {{ auth()->id() }},
        initialMessages: {{ $messages->map(fn($m) => ['id' => $m->id, 'sender_id' => $m->sender_id, 'message' => $m->message, 'created_at' => $m->created_at->format('H:i')])->values()->toJson() }}
     })" x-init="init()">

    {{-- Conversation list --}}
    <div class="w-72 border-r border-border flex flex-col shrink-0">
        <div class="px-4 py-3 border-b border-border">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Conversations</p>
        </div>
        <div class="flex-1 overflow-y-auto">
            <template x-if="partners.length === 0">
                <p class="px-4 py-6 text-sm text-muted">No contacts available yet.</p>
            </template>
            <template x-for="p in partners" :key="p.id">
                <button @click="select(p.id)"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-surface/60 transition-colors border-b border-border/50"
                    :class="activeId === p.id ? 'bg-surface' : ''">
                    <img :src="p.avatar" class="w-9 h-9 rounded-full object-cover shrink-0">
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm font-medium text-primary truncate" x-text="p.name"></span>
                    </span>
                    <span x-show="unread[p.id]" class="w-5 h-5 flex items-center justify-center bg-danger text-white text-[10px] font-bold rounded-full" x-text="unread[p.id]"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Thread --}}
    <div class="flex-1 flex flex-col min-w-0">
        <template x-if="!activeId">
            <div class="flex-1 flex items-center justify-center text-muted text-sm">Select a conversation to start chatting</div>
        </template>

        <template x-if="activeId">
            <div class="flex-1 flex flex-col min-h-0">
                <div class="px-5 py-3 border-b border-border">
                    <p class="text-sm font-semibold text-primary" x-text="activePartner()?.name"></p>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3" x-ref="scrollArea">
                    <template x-for="m in messages" :key="m.id">
                        <div class="flex" :class="m.sender_id === meId ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm"
                                 :class="m.sender_id === meId ? 'bg-brand-600 text-white rounded-br-sm' : 'bg-surface text-primary rounded-bl-sm'">
                                <p x-text="m.message" class="whitespace-pre-wrap break-words"></p>
                                <p class="text-[10px] mt-1 opacity-60" x-text="m.created_at"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <form @submit.prevent="send" class="border-t border-border px-4 py-3 flex items-center gap-3">
                    <input type="text" x-model="draft" placeholder="Type a message…" autocomplete="off"
                        class="flex-1 px-4 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/30">
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
                        Send
                    </button>
                </form>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function chatPanel(config) {
    return {
        partners: config.partners,
        activeId: config.activeId,
        unread: config.unread,
        messages: config.initialMessages,
        draft: '',
        meId: config.meId,
        pollTimer: null,

        init() {
            this.scrollToBottom();
            this.pollTimer = setInterval(() => this.poll(), 3000);
        },

        activePartner() {
            return this.partners.find(p => p.id === this.activeId);
        },

        select(id) {
            window.location.href = config.indexUrl + '?with=' + id;
        },

        send() {
            if (!this.draft.trim()) return;
            const body = this.draft;
            this.draft = '';

            fetch(config.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ recipient_id: this.activeId, message: body }),
            })
            .then(r => r.json())
            .then(data => {
                this.messages.push({
                    id: data.message.id,
                    sender_id: data.message.sender_id,
                    message: data.message.message,
                    created_at: new Date(data.message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                });
                this.$nextTick(() => this.scrollToBottom());
            });
        },

        poll() {
            if (!this.activeId) return;
            const lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;

            fetch(config.pollUrl + '?with=' + this.activeId + '&after_id=' + lastId)
                .then(r => r.json())
                .then(data => {
                    if (data.messages.length) {
                        data.messages.forEach(m => {
                            this.messages.push({
                                id: m.id,
                                sender_id: m.sender_id,
                                message: m.message,
                                created_at: new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                            });
                        });
                        this.unread[this.activeId] = 0;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });
        },

        scrollToBottom() {
            if (this.$refs.scrollArea) {
                this.$refs.scrollArea.scrollTop = this.$refs.scrollArea.scrollHeight;
            }
        },
    };
}
</script>
@endpush
