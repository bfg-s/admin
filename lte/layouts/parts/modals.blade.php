<span x-data="adminModals('{{ route('admin.load_modal') }}')">
    <template x-if="loading">
        <div class="velmld-overlay velmld-full-screen" style="background-color: rgba(0, 0, 0, 0.3);">
            <div class="velmld-spinner">
                <svg data-v-27234dc7="" version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" xml:space="preserve">
                    <path fill="#FFFFFF" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                        <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform>
                    </path>
                </svg>
            </div>
        </div>
    </template>
    <template x-for="(modal, index) in modals">
        <div
            :key="modal.key"
            x-ref="modal.key"
            class="modal fade"
            data-modal
            :data-modal-key="modal.key"
        ><div
                :class="{
                'modal-dialog': true,
                'modal-xl': modal.options.size === 'extra',
                'modal-lg': modal.options.size === 'big',
                'modal-sm': modal.options.size === 'small',
            }" x-html="modal.content"
            ></div></div>
    </template>
</span>
