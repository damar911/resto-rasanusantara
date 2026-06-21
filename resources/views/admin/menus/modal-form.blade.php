{{-- Modal Tambah/Edit Menu --}}
<div
    x-show="modalOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div @click="modalOpen = false" class="absolute inset-0 bg-slate-900/50"></div>

    <div class="relative bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto z-10">
        <div class="sticky top-0 bg-white border-b border-emerald-100 px-5 py-4 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Kelola Rincian Menu</h3>
            <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form
            :action="isEdit ? `/admin/menus/${form.id}` : '{{ route('admin.menus.store') }}'"
            method="POST"
            enctype="multipart/form-data"
            class="p-5 space-y-4"
        >
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Nama Menu</label>
                <input type="text" name="name" x-model="form.name" required class="input-field">
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Kategori</label>
                <select name="category_id" x-model="form.category_id" required class="input-field">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Harga (Rp)</label>
                <input type="number" name="price" x-model="form.price" required min="0" class="input-field">
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Deskripsi Hidangan</label>
                <textarea name="description" x-model="form.description" rows="3" class="input-field"></textarea>
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Foto Menu</label>
                <input type="file" name="photo" accept="image/*" class="input-field">
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Status Ketersediaan</label>
                <select name="availability" x-model="form.availability" required class="input-field">
                    <option value="available">Tersedia</option>
                    <option value="sold_out">Habis / Kosong</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="modalOpen = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>
