<form action="{{ route('upload.images') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="images[]" multiple>
    <button type="submit">Subir imágenes</button>
</form>