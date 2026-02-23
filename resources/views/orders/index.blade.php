<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Orders
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-700">Sales Orders</h3>
                <button onclick="openCreateModal()"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                    + New Order
                </button>
            </div>

            <div class="bg-white shadow-xl rounded-2xl border overflow-x-auto">
                <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end mt-5 px-5">

                    <div>
                        <label class="text-xs text-gray-500">From</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                            class="border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">To</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="border rounded px-3 py-2">
                    </div>

                    <button class="px-4 py-2 bg-indigo-600 text-white rounded shadow">
                        Filter
                    </button>

                    <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-200 rounded">
                        Reset
                    </a>

                </form>
   {{-- TABLE --}}
                <table class="w-full text-sm">
                    <thead class="bg-indigo-50 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Total</th>
                            <th class="px-6 py-4 text-left">Paid</th>
                            <th class="px-6 py-4 text-left">Due(Return)</th>
                            <th class="px-6 py-4 text-left">Purchase</th>
                            <th class="px-6 py-4 text-left">Profit</th>
                            <th class="px-6 py-4 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @foreach ($orders as $o)

                            @php
                                $purchase = $o->items->sum(fn($i) => $i->purchase_price * $i->quantity);
                                $profit   = $o->grand_total - $purchase;
                            @endphp

                            {{-- MAIN ROW --}}
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $o->order_date }}</td>
                                <td class="px-6 py-4">{{ $o->grand_total }}</td>
                                <td class="px-6 py-4 text-green-600">{{ $o->paid_amount }}</td>
                                <td class="px-6 py-4 text-red-600">{{ $o->due_amount }}</td>
                                <td class="px-6 py-4">{{ number_format($purchase,2) }}</td>
                                <td class="px-6 py-4 font-semibold {{ $profit < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ number_format($profit,2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="toggleDetails({{ $o->id }})"
                                        class="px-3 py-1.5 text-xs bg-gray-200 rounded hover:bg-gray-300">
                                        Details
                                    </button>
                                </td>
                            </tr>

                            {{-- DETAILS ROW --}}
                            <tr id="details-{{ $o->id }}" class="hidden bg-gray-50">
                                <td colspan="7" class="px-6 py-4">

                                    <div class="rounded-xl border bg-white overflow-hidden">

                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Product</th>
                                                    <th class="px-4 py-2 text-left">Qty</th>
                                                    <th class="px-4 py-2 text-left">Sell Price</th>
                                                    <th class="px-4 py-2 text-left">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y">
                                                @foreach ($o->items as $it)
                                                    <tr>
                                                        <td class="px-4 py-2">{{ $it->product->name ?? '-' }}</td>
                                                        <td class="px-4 py-2">{{ $it->quantity }}</td>
                                                        <td class="px-4 py-2">{{ $it->sell_price }}</td>
                                                        <td class="px-4 py-2">{{ $it->total }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="p-4 text-sm grid grid-cols-4 gap-3 border-t bg-gray-50">
                                            <div>Subtotal: <b>{{ $o->subtotal }}</b></div>
                                            <div>Discount: <b>{{ $o->discount }}</b></div>
                                            <div>VAT: <b>{{ $o->vat_amount }}</b></div>
                                            <div class="text-red-600">Due: <b>{{ $o->due_amount }}</b></div>
                                        </div>

                                    </div>

                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                    <tfoot class="bg-indigo-50 font-semibold text-sm">
                        <tr>
                            <td class="px-6 py-4 text-right">Totals:</td>
                            <td class="px-6 py-4">{{ number_format($totals['grand'],2) }}</td>
                            <td class="px-6 py-4 text-green-700">{{ number_format($totals['paid'],2) }}</td>
                            <td class="px-6 py-4 text-red-700">{{ number_format($totals['due'],2) }}</td>
                            <td class="px-6 py-4">{{ number_format($totals['purchase'],2) }}</td>
                            <td class="px-6 py-4 text-blue-700">{{ number_format($totals['profit'],2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    <div id="orderModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl">

            <div class="px-6 py-4 border-b flex justify-between">
                <h3 class="text-lg font-semibold">Place Order</h3>
                <button onclick="closeModal()" class="text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('orders.store') }}" class="p-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-4">

                    {{-- LEFT COLUMN --}}
                    <div class="space-y-4">

                        <input type="date" name="order_date" class="w-full border rounded-xl px-4 py-2">

                        <div class="grid grid-cols-2 gap-3">
                            <input type="number" name="discount" placeholder="Discount"
                                class="border rounded px-3 py-2 calcField">

                            <input type="number" name="vat_percent" placeholder="VAT %"
                                class="border rounded px-3 py-2 calcField">

                            <input type="number" name="paid_amount" placeholder="Paid"
                                class="border rounded px-3 py-2 calcField col-span-2">
                        </div>

                        {{-- SUMMARY --}}
                        <div class="bg-gray-50 border rounded-xl p-4 text-sm space-y-1">
                            <div>Subtotal: <span id="sumSubtotal">0</span></div>
                            <div>VAT: <span id="sumVat">0</span></div>
                            <div>Grand Total: <span id="sumGrand">0</span></div>
                            <div class="text-green-600">Paid: <span id="sumPaid">0</span></div>
                            <div class="text-red-600">Due: <span id="sumDue">0</span></div>
                        </div>

                    </div>


                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-3">

                        <div id="itemsContainer" class="space-y-2">
                            <div class="grid grid-cols-3 gap-2 itemRow">

                                <select name="products[0][product_id]" class="border rounded px-2 py-2 productSelect">
                                    <option value="">Select product</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="number" name="products[0][qty]" placeholder="Qty"
                                    class="border rounded px-2 py-2 qtyField">

                                <input type="number" name="products[0][price]"
                                    class="border rounded px-2 py-2 priceField" readonly>

                            </div>
                        </div>

                        <button type="button" onclick="addRow()" class="text-indigo-600 text-sm">
                            + Add Item
                        </button>

                    </div>

                </div>

                <div class="flex justify-end pt-5 mt-4 border-t">
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow">
                        Save Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let index = 1;
        const modal = document.getElementById('orderModal');

        function openCreateModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loadPrices();
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function addRow() {

            const container = document.getElementById('itemsContainer');

            const row = document.createElement('div');
            row.className = 'grid grid-cols-3 gap-2 itemRow';

            row.innerHTML = `
                <select name="products[${index}][product_id]"
                        class="border rounded px-2 py-2 productSelect">
                    <option value="">Select product</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}"
                                data-price="{{ $p->sell_price }}">
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="products[${index}][qty]"
                       placeholder="Qty"
                       class="border rounded px-2 py-2 qtyField">

                <input type="number" name="products[${index}][price]"
                       class="border rounded px-2 py-2 priceField"
                       readonly>
            `;

            container.appendChild(row);
            index++;
        }

        function loadPrices() {
            document.querySelectorAll('.productSelect').forEach(select => {
                const option = select.selectedOptions[0];
                if (!option) return;
                const price = option.dataset.price;
                const row = select.closest('.itemRow');
                row.querySelector('.priceField').value = price || 0;
            });
        }

        function calculate() {
            let subtotal = 0;

            document.querySelectorAll('.itemRow').forEach(row => {
                const qty = parseFloat(row.querySelector('.qtyField').value || 0);
                const price = parseFloat(row.querySelector('.priceField').value || 0);
                subtotal += qty * price;
            });

            const discount = parseFloat(document.querySelector('[name="discount"]').value || 0);
            const vatPercent = parseFloat(document.querySelector('[name="vat_percent"]').value || 0);
            const paid = parseFloat(document.querySelector('[name="paid_amount"]').value || 0);

            const afterDiscount = subtotal - discount;
            const vat = afterDiscount * vatPercent / 100;
            const grand = afterDiscount + vat;
            const due = grand - paid;

            document.getElementById('sumSubtotal').innerText = subtotal.toFixed(2);
            document.getElementById('sumVat').innerText = vat.toFixed(2);
            document.getElementById('sumGrand').innerText = grand.toFixed(2);
            document.getElementById('sumPaid').innerText = paid.toFixed(2);
            document.getElementById('sumDue').innerText = due.toFixed(2);
        }

        document.addEventListener('change', e => {
            if (e.target.classList.contains('productSelect')) {
                loadPrices();
                calculate();
            }
            if (e.target.classList.contains('qtyField') || e.target.classList.contains('calcField')) {
                calculate();
            }
        });
    </script>

 {{-- MODAL JS --}}
    <script>
        function toggleDetails(id){
            const row=document.getElementById('details-'+id);
            if(row) row.classList.toggle('hidden');
        }
    </script>


</x-app-layout>
