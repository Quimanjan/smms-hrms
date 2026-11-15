<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold leading-tight text-red-700 sm:text-3xl md:text-4xl">Messages</h2>
    </x-slot>

    <div class="mx-auto max-w-[1920px] px-4 pb-12 pt-2 sm:px-6 lg:px-10">
        <section class="msg" x-data="messenger(@js($people), @js($startWith))" x-init="init()" :class="{ 'is-open': activeUser }">

            <aside class="msg-side" aria-label="Conversations">
                <label class="msg-find">
                    <span class="sr-only">Find a person</span>
                    <input type="search" x-model="find" placeholder="Find a person to message">
                </label>

                <ul class="msg-people" x-show="find.trim()">
                    <template x-for="p in matches" :key="p.id">
                        <li><button type="button" @click="openWith(p)"><strong x-text="p.name"></strong><small x-text="p.role"></small></button></li>
                    </template>
                    <li class="msg-empty" x-show="!matches.length">No one matches that name.</li>
                </ul>

                <ul class="msg-convos" x-show="!find.trim()">
                    <template x-for="c in conversations" :key="c.id">
                        <li>
                            <button type="button" @click="openConversation(c)" :class="{ 'is-active': activeUser && activeUser.id === c.user_id }">
                                <span class="msg-who"><strong x-text="c.name"></strong><small x-text="c.when"></small></span>
                                <span class="msg-last" x-text="c.last"></span>
                                <b class="msg-unread" x-show="c.unread" x-text="c.unread"></b>
                            </button>
                        </li>
                    </template>
                    <li class="msg-empty" x-show="loaded && !conversations.length">No messages yet. Find a person above to start.</li>
                </ul>
            </aside>

            <div class="msg-main" aria-live="polite">
                <template x-if="!activeUser">
                    <div class="msg-placeholder">
                        <h3>Send a message</h3>
                        <p>Choose a conversation, or find a person to message HR, Accounting, Admin, or a co-employee.</p>
                    </div>
                </template>

                <template x-if="activeUser">
                    <div class="msg-thread">
                        <header class="msg-thread-head">
                            <button type="button" class="msg-back" @click="activeUser = null; activeId = null" aria-label="Back to conversations">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                            </button>
                            <div><strong x-text="activeUser.name"></strong><small x-text="activeUser.role"></small></div>
                        </header>

                        <ol class="msg-list" x-ref="list">
                            <template x-for="m in messages" :key="m.id">
                                <li :class="m.mine ? 'is-mine' : ''">
                                    <p x-text="m.body"></p>
                                    <time x-text="m.when"></time>
                                </li>
                            </template>
                            <li class="msg-empty" x-show="!messages.length">Say hello. Your first message starts the conversation.</li>
                        </ol>

                        <form class="msg-compose" @submit.prevent="send()">
                            <label class="sr-only" for="msg-body">Message</label>
                            <textarea id="msg-body" x-model="draft" rows="1" maxlength="2000" placeholder="Write a message"
                                @keydown.enter.prevent="if (!$event.shiftKey) send(); else draft += '\n'"></textarea>
                            <button type="submit" :disabled="sending || !draft.trim()">Send</button>
                        </form>
                        <p class="msg-error" x-show="error" x-text="error"></p>
                    </div>
                </template>
            </div>
        </section>
    </div>

    <script>
        function messenger(people, startWith) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const urls = {
                list: @js(route('messages.conversations')),
                thread: @js(route('messages.show', ['conversation' => '__ID__'])),
                send: @js(route('messages.store')),
            };

            return {
                people, find: '', conversations: [], loaded: false,
                activeUser: null, activeId: null, messages: [], draft: '', sending: false, error: '',
                timer: null,

                get matches() {
                    const q = this.find.trim().toLowerCase();
                    return this.people.filter((p) => p.name.toLowerCase().includes(q)).slice(0, 12);
                },

                async init() {
                    await this.refresh();
                    if (startWith) {
                        const p = this.people.find((x) => x.id === startWith);
                        if (p) this.openWith(p);
                    }
                    // Check for new messages every 5 seconds.
                    this.timer = setInterval(() => this.tick(), 5000);
                },

                async refresh() {
                    try {
                        const r = await fetch(urls.list, { headers: { Accept: 'application/json' } });
                        const data = await r.json();
                        this.conversations = data.conversations;
                        this.loaded = true;
                        // Keep the menu's unread badge in step.
                        const unread = this.conversations.reduce((n, c) => n + (c.id === this.activeId ? 0 : c.unread), 0);
                        document.querySelectorAll('.sb-badge').forEach((b) => { b.textContent = unread; b.hidden = unread === 0; });
                    } catch (e) {}
                },

                async tick() {
                    await this.refresh();
                    if (this.activeId) await this.loadThread(true);
                },

                openWith(p) {
                    this.find = '';
                    const c = this.conversations.find((x) => x.user_id === p.id);
                    if (c) return this.openConversation(c);
                    this.activeUser = { id: p.id, name: p.name, role: p.role };
                    this.activeId = null;
                    this.messages = [];
                    this.$nextTick(() => document.getElementById('msg-body')?.focus());
                },

                async openConversation(c) {
                    this.activeUser = { id: c.user_id, name: c.name, role: c.role };
                    this.activeId = c.id;
                    this.messages = [];
                    await this.loadThread(false);
                    c.unread = 0;
                    await this.refresh();
                    this.$nextTick(() => document.getElementById('msg-body')?.focus());
                },

                async loadThread(onlyNew) {
                    const after = onlyNew && this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                    try {
                        const r = await fetch(urls.thread.replace('__ID__', this.activeId) + '?after=' + after, { headers: { Accept: 'application/json' } });
                        if (!r.ok) return;
                        const data = await r.json();
                        if (data.messages.length) {
                            this.messages = onlyNew ? this.messages.concat(data.messages) : data.messages;
                            this.$nextTick(() => { const l = this.$refs.list; if (l) l.scrollTop = l.scrollHeight; });
                        }
                    } catch (e) {}
                },

                async send() {
                    const body = this.draft.trim();
                    if (!body || this.sending) return;
                    this.sending = true; this.error = '';
                    try {
                        const r = await fetch(urls.send, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({ to: this.activeUser.id, body }),
                        });
                        if (!r.ok) throw new Error('The message was not sent. Check your connection and try again.');
                        const data = await r.json();
                        this.draft = '';
                        this.activeId = data.conversation;
                        await this.loadThread(true);
                        await this.refresh();
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.sending = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
