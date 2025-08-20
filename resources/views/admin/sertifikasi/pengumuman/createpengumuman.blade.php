<form action="{{ route('admin.sertification.assessment-announcement.store', $sertification->id) }}" class="mt-4 flex flex-col gap-2"
    method="POST" x-data="{ error: '', files: null }" enctype="multipart/form-data">
    @csrf
    <input type="text" hidden value="{{ $sertification->id }}" name="sertification_id">
    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1" for="rincian_pengumuman_asesmen">Rincian</label>
    @include('layouts.custom-rich-editor', [
        'inputName' => 'rincian_pengumuman_asesmen',
        'initialValue' => old('rincian_pengumuman_asesmen', $sertification?->rincian_pengumuman_asesmen ?? 'Silahkan buat pengumuman...'),
    ])
    @error('rincian_pengumuman_asesmen')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror

    <label id="pengumuman_asesmen_attachment_file" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Lampiran (maks 5
        file)</label>
    {{-- <p class="text-sm">Jumlah file lama: <span x-text="$store.asesmen.jumlahFileLama"></span></p> --}}
    
    <input id="pengumuman_asesmen_attachment_file" type="file" name="pengumuman_asesmen_attachment_file[]" multiple accept=".jpg,.jpeg,.png,.pdf,.docx,.pptx,.xlx"
        class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"/>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">
        Tipe file: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLSX.
    </p>
    <template x-if="error">
        <p class="text-red-600 text-xs mt-1" x-text="error"></p>
    </template>
    @error('pengumuman_asesmen_attachment_file.*')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    @error('pengumuman_asesmen_attachment_file')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    
    <button type="submit"
        class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
        Buat Pengumuman
    </button>
</form>
