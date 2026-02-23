<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Categories
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Top Bar --}}
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-700">Manage Categories</h3>
                <button onclick="openCreateModal()"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                    + Add Category
                </button>
            </div>

            {{-- Table --}}
<div class="w-full bg-white shadow-xl rounded-2xl border">

    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Category</th>
                    <th class="px-6 py-4 text-left">Slug</th>
                    <th class="px-6 py-4 text-left">Image</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse($categories as $cat)
                <tr class="hover:bg-indigo-50/40 transition">

                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $cat->name }}
                    </td>

                    <td class="px-6 py-4 text-gray-500">
                        {{ $cat->slug }}
                    </td>

                    <td class="px-6 py-4">
                        @if($cat->image_path)
                            <img src="{{ asset('storage/'.$cat->image_path) }}"
                                 class="h-12 w-12 object-cover rounded-xl border shadow-sm">
                        @else
                            <div class="h-12 w-12 flex items-center justify-center bg-gray-100 rounded-xl text-gray-400 text-xs">
                                No image
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">

                        <div class="flex justify-end gap-2">

                            <button onclick='openEditModal(@json($cat))'
                                class="px-4 py-1.5 text-xs bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('categories.destroy',$cat->id) }}">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this category?')"
                                    class="px-4 py-1.5 text-xs bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-14 text-gray-400">
                        No categories found
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>
    </div>
</div>

    {{-- Modal --}}
<div id="categoryModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl">

        {{-- Header --}}
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">
                Add Category
            </h3>

            <button onclick="closeModal()"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">
                &times;
            </button>
        </div>

        {{-- Body --}}
        <form id="categoryForm"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="POST">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Category Name
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       placeholder="Enter category name"
                       class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Category Image
                </label>
                <input type="file"
                       name="image"
                       class="w-full border rounded-xl px-4 py-2.5 bg-white">
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 pt-3 border-t">
                <button type="button"
                        onclick="closeModal()"
                        class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white shadow hover:bg-indigo-700 transition">
                    Save Category
                </button>
            </div>
        </form>

    </div>
</div>

    {{-- Scripts --}}
    <script>
        const modal = document.getElementById('categoryModal');

        function openCreateModal(){
            document.getElementById('modalTitle').innerText="Add Category";
            document.getElementById('categoryForm').action="../../auth/categories";
            document.getElementById('methodField').value="POST";

            document.getElementById('name').value='';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(cat){
            document.getElementById('modalTitle').innerText="Edit Category";
            document.getElementById('categoryForm').action="../../auth/categories/"+cat.id;
            document.getElementById('methodField').value="PUT";

            document.getElementById('name').value=cat.name ?? '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

</x-app-layout>
