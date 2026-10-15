<section id="returningGuestSearch" class="mb-5 overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm sm:mb-6">
    <div class="flex items-start gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-4 sm:px-6">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white"><i class="fas fa-clock-rotate-left text-lg" aria-hidden="true"></i></span>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Returning guest</h2>
            <p class="mt-1 text-sm text-gray-600">Find a previous stay and use the guest's details for a new booking or check-in.</p>
        </div>
    </div>
    <div class="p-4 sm:p-6">
        <label for="searchPhone" class="mb-2 block text-sm font-semibold text-gray-700">Phone, NID or passport number</label>
        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
            <input type="search" id="searchPhone" autocomplete="off" placeholder="e.g. 01XXXXXXXXX" class="min-w-0 flex-1 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            <button type="button" id="guestSearchButton" onclick="searchCustomer()" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-60"><i class="fas fa-search mr-2" aria-hidden="true"></i>Find guest</button>
        </div>
        <div id="customerSearchResults" class="mt-4" aria-live="polite"></div>
        <p id="selectedGuestNotice" class="mt-3 hidden rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800" role="status"></p>
    </div>
</section>
