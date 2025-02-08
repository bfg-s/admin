<div class="hidden lg:block lg:pl-3.5" x-data="globalSearch">
    <label for="topbar-search" class="sr-only">Search</label>
    <div class="relative mt-1 lg:w-96">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
        </div>
        <input
            x-model="q"
            aria-label="@lang('admin.search')"
            class="global_search_input_focus bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="@lang('admin.search')..."
            type="text"
            @blur="blur"
            @mouseup="show_if_has"
            @keyup="key_up_nav"
            @keyup.enter.prevent="got_to_search"
            @search="click_cancel"
        >
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
