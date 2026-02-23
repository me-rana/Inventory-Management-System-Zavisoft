<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Products
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top Bar --}}
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-700">Manage Products</h3>
                <button onclick="openCreateModal()"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                    + Add Product
                </button>
            </div>

            {{-- Table --}}
            <div class="w-full bg-white shadow-xl rounded-2xl border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-left">Category</th>
                                <th class="px-6 py-4 text-left">Prices</th>
                                <th class="px-6 py-4 text-left">Stock</th>
                                <th class="px-6 py-4 text-left">Image</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            @forelse($products as $p)
                            <tr class="hover:bg-indigo-50/40 transition">

                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $p->name }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $p->category->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    Sell: {{ $p->sell_price }} <br>
                                    Buy: {{ $p->purchase_price }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100">
                                        {{ $p->quantity }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($p->image_path)
                                        <img src="{{ asset('storage/'.$p->image_path) }}"
                                             class="h-12 w-12 object-cover rounded-xl border shadow">
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">

                                        <button onclick='openEditModal(@json($p))'
                                                class="px-4 py-1.5 text-xs bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                                            Edit
                                        </button>

                                        <form method="POST"
                                              action="{{ route('products.destroy',$p->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Delete product?')"
                                                    class="px-4 py-1.5 text-xs bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-14 text-gray-400">
                                    No products yet
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    <div id="productModal"
         class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl">

            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">
                    Add Product
                </h3>
                <button onclick="closeModal()" class="text-gray-400 text-xl">&times;</button>
            </div>

            <form id="productForm"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                <input type="text" name="name" id="name"
                       placeholder="Product name"
                       class="w-full border rounded-xl px-4 py-2">

                <select name="category_id" id="category_id"
                        class="w-full border rounded-xl px-4 py-2">
                    <option value="">Select category</option>
                    @foreach($categories as $id=>$name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="sell_price" id="sell_price"
                           placeholder="Sell price"
                           class="border rounded-xl px-4 py-2">

                    <input type="number" name="purchase_price" id="purchase_price"
                           placeholder="Purchase price"
                           class="border rounded-xl px-4 py-2">
                </div>

                <input type="number" name="quantity" id="quantity"
                       placeholder="Stock quantity"
                       class="w-full border rounded-xl px-4 py-2">

                <input type="file" name="image"
                       class="w-full border rounded-xl px-4 py-2 bg-white">

                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-5 py-2 bg-gray-200 rounded-lg">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                        Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        const modal = document.getElementById('productModal');

        function openCreateModal(){
            document.getElementById('modalTitle').innerText="Add Product";
            document.getElementById('productForm').action="../../auth/products";
            document.getElementById('methodField').value="POST";

            document.getElementById('name').value='';
            document.getElementById('sell_price').value='';
            document.getElementById('purchase_price').value='';
            document.getElementById('quantity').value='';
            document.getElementById('category_id').value='';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(p){
            document.getElementById('modalTitle').innerText="Edit Product";
            document.getElementById('productForm').action="../../auth/products/"+p.id;
            document.getElementById('methodField').value="PUT";

            document.getElementById('name').value=p.name ?? '';
            document.getElementById('sell_price').value=p.sell_price ?? '';
            document.getElementById('purchase_price').value=p.purchase_price ?? '';
            document.getElementById('quantity').value=p.quantity ?? '';
            document.getElementById('category_id').value=p.category_id ?? '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

</x-app-layout>
