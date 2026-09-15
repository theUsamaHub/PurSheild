<div
    x-data="commandPalette()"
    x-on:keydown.window.prevent.cmd.k="toggle()"
    x-on:keydown.window.prevent.ctrl.k="toggle()"
    x-on:keydown.escape="close()"
    x-on:toggle-command-palette.window="open = true; $nextTick(() => { query = ''; activeIndex = 0; $refs.searchInput?.focus(); })"
    x-cloak
>
    <div x-show="open" x-transition.opacity class="position-fixed" style="inset:0;z-index:1055;background:rgba(0,0,0,0.5);" x-on:click="close()"></div>

    <div x-show="open" x-transition class="position-fixed" style="top:12%;left:50%;transform:translateX(-50%);z-index:1056;width:100%;max-width:520px;">
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-secondary flex-shrink-0" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        <input
                            type="text"
                            class="form-control border-0 shadow-none px-0"
                            x-ref="searchInput"
                            x-model="query"
                            x-on:keydown="handleKeydown"
                            placeholder="Search pages..."
                            style="outline:none;font-size:0.95rem;caret-color:var(--bs-primary);"
                        >
                        <kbd class="bg-light px-2 py-1 rounded border text-muted flex-shrink-0" style="font-size:0.65rem;">ESC</kbd>
                    </div>
                </div>
                <ul class="list-unstyled mb-0" style="max-height:380px;overflow-y:auto;">
                    <template x-for="(item, index) in filtered" :key="item.url">
                        <li>
                            <a
                                :href="item.url"
                                class="d-flex align-items-center px-3 py-2 text-decoration-none border-bottom border-light"
                                :class="index === activeIndex ? 'bg-primary text-white' : 'text-body hover-bg-light'"
                                @click.prevent="go(item)"
                                @mouseenter="activeIndex = index"
                                style="transition:background 0.1s;"
                            >
                                <i :class="item.icon" class="me-3 text-center flex-shrink-0" style="width:1.2rem;font-size:0.9rem;"></i>
                                <span class="flex-grow-1" x-text="item.name" style="font-size:0.85rem;"></span>
                                <span class="badge fw-normal" :class="index === activeIndex ? 'bg-white text-primary bg-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary'" x-text="item.category" style="font-size:0.6rem;"></span>
                            </a>
                        </li>
                    </template>
                    <li x-show="filtered.length === 0 && query.length > 0">
                        <div class="px-3 py-5 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-muted d-block mx-auto mb-2" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                            <small class="text-muted">No results for "<span x-text="query" class="fw-medium text-body"></span>"</small>
                        </div>
                    </li>
                </ul>
                <div class="px-3 py-2 border-top d-flex gap-3 justify-content-end bg-body-tertiary" style="font-size:0.6rem;">
                    <span class="text-muted"><kbd class="bg-white text-black px-1 rounded border">↑</kbd> <kbd class="bg-white text-black px-1 rounded border">↓</kbd> Navigate</span>
                    <span class="text-muted"><kbd class="bg-white text-black px-1 rounded border">↵</kbd> Open</span>
                    <span class="text-muted"><kbd class="bg-white text-black px-1 rounded border">ESC</kbd> Close</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const el = document.querySelector('[x-data="commandPalette()"]');
        if (el && el.__x) {
            el.__x.toggle();
        }
    }
});

function commandPalette() {
    return {
        open: false,
        query: '',
        activeIndex: 0,
        pages: [
            { name: 'Dashboard', url: '{{ route("dashboard") }}', icon: 'bi bi-grid-1x2', category: 'General' },
            { name: 'Profile', url: '{{ route("profile.edit") }}', icon: 'bi bi-person', category: 'General' },
            @if(auth()->user()->hasRole('admin'))
            { name: 'Admin Dashboard', url: '{{ route("admin.dashboard") }}', icon: 'bi bi-speedometer2', category: 'Admin' },
            { name: 'Verify Vets & Shelters', url: '{{ route("admin.verification.index") }}', icon: 'bi bi-patch-check', category: 'Admin' },
            { name: 'Users', url: '{{ route("admin.users.index") }}', icon: 'bi bi-people', category: 'Admin' },
            { name: 'Roles', url: '{{ route("admin.roles.index") }}', icon: 'bi bi-shield-check', category: 'Admin' },
            { name: 'Reviews', url: '{{ route("admin.reviews.index") }}', icon: 'bi bi-star', category: 'Admin' },
            { name: 'Products', url: '{{ route("admin.products.index") }}', icon: 'bi bi-box-seam', category: 'E-Commerce' },
            { name: 'Product Categories', url: '{{ route("admin.product-categories.index") }}', icon: 'bi bi-grid', category: 'E-Commerce' },
            { name: 'Orders', url: '{{ route("admin.orders.index") }}', icon: 'bi bi-bag', category: 'E-Commerce' },
            { name: 'SKU Templates', url: '{{ route("admin.sku-templates.index") }}', icon: 'bi bi-upc-scan', category: 'E-Commerce' },
            { name: 'Care Content', url: '{{ route("admin.care-content.index") }}', icon: 'bi bi-journal-richtext', category: 'E-Commerce' },
            { name: 'Species', url: '{{ route("admin.species.index") }}', icon: 'bi bi-bug', category: 'Veterinary' },
            { name: 'Breeds', url: '{{ route("admin.breeds.index") }}', icon: 'bi bi-heart', category: 'Veterinary' },
            { name: 'Specializations', url: '{{ route("admin.specializations.index") }}', icon: 'bi bi-star', category: 'Veterinary' },
            { name: 'Contacts', url: '{{ route("admin.contacts.index") }}', icon: 'bi bi-envelope', category: 'Communication' },
            { name: 'Notifications', url: '{{ route("admin.notifications.index") }}', icon: 'bi bi-bell', category: 'System' },
            { name: 'Activity Logs', url: '{{ route("admin.activity-logs.index") }}', icon: 'bi bi-clock-history', category: 'System' },
            { name: 'Sessions', url: '{{ route("admin.sessions.index") }}', icon: 'bi bi-person-badge', category: 'System' },
            { name: 'Maintenance', url: '{{ route("admin.maintenance.index") }}', icon: 'bi bi-shield-exclamation', category: 'System' },
            { name: 'Health Dashboard', url: '{{ route("admin.health.index") }}', icon: 'bi bi-heart-pulse', category: 'System' },
            { name: 'Log Viewer', url: '{{ route("admin.logs.index") }}', icon: 'bi bi-journal-text', category: 'System' },
            { name: 'Backups', url: '{{ route("admin.backup.index") }}', icon: 'bi bi-database', category: 'System' },
            { name: 'Recycle Bin', url: '{{ route("admin.trash.index") }}', icon: 'bi bi-trash', category: 'System' },
            @endif
            @if(auth()->user()->hasRole('owner'))
            { name: 'Owner Dashboard', url: '{{ route("owner.dashboard") }}', icon: 'bi bi-speedometer2', category: 'Owner' },
            { name: 'My Pets', url: '{{ route("owner.pets.index") }}', icon: 'bi bi-heart', category: 'Owner' },
            { name: 'Add Pet', url: '{{ route("owner.pets.create") }}', icon: 'bi bi-plus-circle', category: 'Owner' },
            { name: 'Appointments', url: '{{ route("owner.appointments.index") }}', icon: 'bi bi-calendar-check', category: 'Owner' },
            { name: 'Book Appointment', url: '{{ route("owner.appointments.create") }}', icon: 'bi bi-calendar-plus', category: 'Owner' },
            { name: 'Browse Vets', url: '{{ route("owner.browse-vets") }}', icon: 'bi bi-heartbeat', category: 'Owner' },
            { name: 'Adoption', url: '{{ route("owner.browse-adoption") }}', icon: 'bi bi-bookmark-heart', category: 'Owner' },
            { name: 'Products', url: '{{ route("owner.products.index") }}', icon: 'bi bi-shop', category: 'Owner' },
            { name: 'My Cart', url: '{{ route("owner.cart.index") }}', icon: 'bi bi-cart3', category: 'Owner' },
            { name: 'My Orders', url: '{{ route("owner.orders.index") }}', icon: 'bi bi-receipt', category: 'Owner' },
            { name: 'Care Content', url: '{{ route("owner.care.index") }}', icon: 'bi bi-journal-bookmark', category: 'Owner' },
            { name: 'Profile', url: '{{ route("profile.edit") }}', icon: 'bi bi-person', category: 'Account' },
            @endif
            @if(auth()->user()->hasRole('vet'))
            { name: 'Vet Dashboard', url: '{{ route("vet.dashboard") }}', icon: 'bi bi-speedometer2', category: 'Veterinarian' },
            { name: 'My Appointments', url: '{{ route("vet.appointments.index") }}', icon: 'bi bi-calendar-check', category: 'Veterinarian' },
            { name: 'Availability', url: '{{ route("vet.availability.index") }}', icon: 'bi bi-clock', category: 'Veterinarian' },
            { name: 'My Patients', url: '{{ route("vet.patients.index") }}', icon: 'bi bi-heart-pulse', category: 'Veterinarian' },
            { name: 'Treatments', url: '{{ route("vet.treatments.index") }}', icon: 'bi bi-clipboard2-pulse', category: 'Veterinarian' },
            { name: 'My Reviews', url: '{{ route("vet.reviews.index") }}', icon: 'bi bi-star', category: 'Veterinarian' },
            { name: 'Profile', url: '{{ route("profile.edit") }}', icon: 'bi bi-person', category: 'Account' },
            @endif
            @if(auth()->user()->hasRole('shelter'))
            { name: 'Shelter Dashboard', url: '{{ route("shelter.dashboard") }}', icon: 'bi bi-speedometer2', category: 'Shelter' },
            { name: 'My Listings', url: '{{ route("shelter.listings.index") }}', icon: 'bi bi-bookmark-heart', category: 'Shelter' },
            { name: 'New Listing', url: '{{ route("shelter.listings.create") }}', icon: 'bi bi-plus-circle', category: 'Shelter' },
            { name: 'Applications', url: '{{ route("shelter.applications.index") }}', icon: 'bi bi-envelope', category: 'Shelter' },
            { name: 'Care Status', url: '{{ route("shelter.care-status.index") }}', icon: 'bi bi-clipboard2-pulse', category: 'Shelter' },
            { name: 'My Reviews', url: '{{ route("shelter.reviews.index") }}', icon: 'bi bi-star', category: 'Shelter' },
            { name: 'Profile', url: '{{ route("profile.edit") }}', icon: 'bi bi-person', category: 'Account' },
            @endif
        ],
        get filtered() {
            if (!this.query) return this.pages;
            const q = this.query.toLowerCase();
            const results = this.pages.filter(p =>
                p.name.toLowerCase().includes(q) ||
                p.category.toLowerCase().includes(q)
            );
            return results;
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.query = '';
                this.activeIndex = 0;
                this.$nextTick(() => { if (this.$refs.searchInput) this.$refs.searchInput.focus(); });
            }
        },
        close() { this.open = false; },
        navigate() { if (this.filtered[this.activeIndex]) this.go(this.filtered[this.activeIndex]); },
        next() { this.activeIndex = Math.min(this.activeIndex + 1, this.filtered.length - 1); },
        prev() { this.activeIndex = Math.max(this.activeIndex - 1, 0); },
        go(item) { window.location.href = item.url; },
        handleKeydown(e) {
            if (e.key === 'Enter') { e.preventDefault(); this.navigate(); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); this.next(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); this.prev(); }
        },
    };
}
</script>
