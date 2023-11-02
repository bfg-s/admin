<div class="form-inline ml-3 d-none d-lg-block d-xl-block" x-data="globalSearch">
    <div class="input-group input-group-sm">
        <input
            x-model="q"
            aria-label="Search"
            class="form-control form-control-navbar global_search_input_focus"
            placeholder="Search"
            type="search"
            @blur="blur"
            @mouseup="show_if_has"
            @keyup="key_up_nav"
            @keyup.enter.prevent="got_to_search"
            @search="click_cancel"
        >
        <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <template x-if="q.length && show">
        <div class="search_result">
            <div class="list-group">
                <template x-for="(item, index) in items">
                    <a
                        :key="index" :class="`${i===index?'active':''} select_search_item list-group-item list-group-item-action select_search_item_${index}`"
                        :href="item.href"
                        @click="show=false"
                        @keyup="key_up_nav"
                    >
                        <i x-bind:class="item.icon + ` nav-icon`"></i>
                        <span x-html="item.inner.replace(/p\>/g, 'span>')"></span>
                        <strong>(<span x-text="item.total"></span>)</strong>
                    </a>
                </template>
            </div>
        </div>
    </template>
</div>
