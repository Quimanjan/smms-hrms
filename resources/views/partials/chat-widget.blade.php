{{--
    Chat bubble in the lower right of every staff page. Clicking it opens a small
    chat window: the conversation list (with a search to start a new chat) and,
    after choosing someone, the conversation itself. It remembers whether it was
    open and who you were talking to while you move between pages.
--}}
@php($chatUnread = \App\Http\Controllers\MessageController::unreadFor(auth()->id()))
<div class="cw" x-data="chatWidget({{ $chatUnread }})" x-init="init()" @keydown.escape.window="if (open) close()">
    <section class="cw-panel" x-show="open" x-cloak
             x-transition:enter="cw-anim" x-transition:enter-start="cw-hide" x-transition:enter-end=""
             x-transition:leave="cw-anim" x-transition:leave-start="" x-transition:leave-end="cw-hide"
             role="dialog" aria-label="Messages">

        {{-- Conversation list --}}
        <div class="cw-view" x-show="!activeUser">
            <header class="cw-head">
                <strong>Messages</strong>
                <button type="button" class="cw-icon" @click="close()" aria-label="Close messages">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </header>
            <label class="cw-find">
                <span class="sr-only">Find a person</span>
                <input type="search" x-model="find" x-ref="find" placeholder="Find a person to message">
            </label>
            <ul class="cw-list" x-show="find.trim()">
                <template x-for="p in matches" :key="p.id">
                    <li><button type="button" @click="openWith(p)"><span class="cw-face" x-text="initials(p.name)"></span><span class="cw-who"><strong x-text="p.name"></strong><small x-text="p.role"></small></span></button></li>
                </template>
                <li class="cw-empty" x-show="peopleLoaded && !matches.length">No one matches that name.</li>
            </ul>
            <ul class="cw-list" x-show="!find.trim()">
                <template x-for="c in conversations" :key="c.id">
                    <li>
                        <button type="button" @click="openConversation(c)">
                            <span class="cw-face" x-text="initials(c.name)"></span>
                            <span class="cw-who">
                                <span class="cw-row"><strong x-text="c.name"></strong><small x-text="c.when"></small></span>
                                <span class="cw-last" :class="{ 'is-unread': c.unread }" x-text="c.last"></span>
                            </span>
                            <b class="cw-dot" x-show="c.unread" x-text="c.unread"></b>
                        </button>
                    </li>
                </template>
                <li class="cw-empty" x-show="loaded && !conversations.length">No messages yet. Search for a person above to start a conversation.</li>
            </ul>
        </div>

        {{-- One conversation --}}
        <div class="cw-view" x-show="activeUser" x-cloak>
            <header class="cw-head">
                <button type="button" class="cw-icon" @click="back()" aria-label="Back to conversations">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <span class="cw-face cw-face--sm" x-text="activeUser ? initials(activeUser.name) : ''"></span>
                <span class="cw-who"><strong x-text="activeUser?.name"></strong><small x-text="activeUser?.role"></small></span>
                <button type="button" class="cw-icon" @click="close()" aria-label="Close messages">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </header>
            <ol class="cw-thread" x-ref="thread" aria-live="polite">
                <template x-for="m in messages" :key="m.id">
                    <li :class="m.mine ? 'is-mine' : ''"><p x-text="m.body"></p><time x-text="m.when"></time></li>
                </template>
                <li class="cw-empty" x-show="!messages.length">Say hello. Your first message starts the conversation.</li>
            </ol>
            <p class="cw-error" x-show="error" x-text="error"></p>
            <form class="cw-compose" @submit.prevent="send()">
                <label class="sr-only" for="cw-body">Message</label>
                <textarea id="cw-body" x-ref="body" x-model="draft" rows="1" maxlength="2000" placeholder="Write a message"
                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"></textarea>
                <button type="submit" class="cw-send" :disabled="sending || !draft.trim()" aria-label="Send message">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4l17.4-7.5a1 1 0 000-1.8L3.4 3.6a1 1 0 00-1.4 1.1L4 11l9 1-9 1-2 6.3a1 1 0 001.4 1.1z"/></svg>
                </button>
            </form>
        </div>
    </section>

    <button type="button" class="cw-bubble" @click="open ? close() : show()" :aria-expanded="open.toString()"
            :aria-label="open ? 'Close messages' : (unread ? unread + ' unread messages. Open messages' : 'Open messages')">
        <svg x-show="!open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 01-11.6 7.1L4 20.5l1.4-4.6A8 8 0 1121 12z"/></svg>
        <svg x-show="open" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        <b class="cw-badge" x-show="unread && !open" x-text="unread > 99 ? '99+' : unread" @if(! $chatUnread) x-cloak @endif>{{ $chatUnread }}</b>
    </button>
</div>

<script>
    function chatWidget(initialUnread) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const urls = {
            people: @js(route('messages.people')),
            list: @js(route('messages.conversations')),
            thread: @js(route('messages.show', ['conversation' => '__ID__'])),
            send: @js(route('messages.store')),
        };
        const KEY = 'smms.chat';
        const save = (v) => { try { sessionStorage.setItem(KEY, JSON.stringify(v)); } catch (e) {} };
        const load = () => { try { return JSON.parse(sessionStorage.getItem(KEY) || '{}'); } catch (e) { return {}; } };
        const json = { headers: { Accept: 'application/json' } };

        return {
            open: false, unread: initialUnread, find: '', people: [], peopleLoaded: false,
            conversations: [], loaded: false, activeUser: null, activeId: null,
            messages: [], draft: '', sending: false, error: '', timer: null,

            get matches() {
                const q = this.find.trim().toLowerCase();
                return this.people.filter((p) => p.name.toLowerCase().includes(q)).slice(0, 20);
            },
            initials(name) {
                return (name || '').split(/\s+/).filter(Boolean).slice(0, 2).map((w) => w[0].toUpperCase()).join('');
            },

            async init() {
                const s = load();
                if (s.open) {
                    await this.show(false);
                    if (s.user) await this.resume(s.user);
                }
                this.schedule();
                document.addEventListener('visibilitychange', () => this.schedule());
            },
            // Check often while the window is open, rarely while it is closed.
            schedule() {
                clearInterval(this.timer);
                if (document.hidden) return;
                this.timer = setInterval(() => this.tick(), this.open ? 5000 : 30000);
            },
            remember() { save({ open: this.open, user: this.activeUser }); },

            async show(focus = true) {
                this.open = true;
                this.remember();
                this.schedule();
                await this.refresh();
                if (!this.peopleLoaded) this.loadPeople();
                if (focus) this.$nextTick(() => (this.activeUser ? this.$refs.body : this.$refs.find)?.focus());
            },
            close() { this.open = false; this.remember(); this.schedule(); },
            back() { this.activeUser = null; this.activeId = null; this.messages = []; this.remember(); this.refresh(); },

            async loadPeople() {
                try { const r = await fetch(urls.people, json); this.people = (await r.json()).people; } catch (e) {}
                this.peopleLoaded = true;
            },
            async refresh() {
                try {
                    const r = await fetch(urls.list, json);
                    if (!r.ok) return;
                    const data = await r.json();
                    this.conversations = data.conversations;
                    this.loaded = true;
                    this.unread = this.conversations.reduce((n, c) => n + (this.open && c.id === this.activeId ? 0 : c.unread), 0);
                } catch (e) {}
            },
            async tick() {
                await this.refresh();
                if (this.open && this.activeId) await this.loadThread(true);
            },

            async resume(user) {
                const c = this.conversations.find((x) => x.user_id === user.id);
                if (c) return this.openConversation(c, false);
                this.activeUser = user;
            },
            openWith(p) {
                this.find = '';
                const c = this.conversations.find((x) => x.user_id === p.id);
                if (c) return this.openConversation(c);
                this.activeUser = { id: p.id, name: p.name, role: p.role };
                this.activeId = null; this.messages = []; this.error = '';
                this.remember();
                this.$nextTick(() => this.$refs.body?.focus());
            },
            async openConversation(c, focus = true) {
                this.activeUser = { id: c.user_id, name: c.name, role: c.role };
                this.activeId = c.id; this.messages = []; this.error = '';
                this.remember();
                await this.loadThread(false);
                await this.refresh();
                if (focus) this.$nextTick(() => this.$refs.body?.focus());
            },
            async loadThread(onlyNew) {
                const after = onlyNew && this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                try {
                    const r = await fetch(urls.thread.replace('__ID__', this.activeId) + '?after=' + after, json);
                    if (!r.ok) return;
                    const data = await r.json();
                    if (data.messages.length || !onlyNew) {
                        this.messages = onlyNew ? this.messages.concat(data.messages) : data.messages;
                        this.$nextTick(() => { const l = this.$refs.thread; if (l) l.scrollTop = l.scrollHeight; });
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
                    this.$nextTick(() => this.$refs.body?.focus());
                }
            },
        };
    }
</script>
