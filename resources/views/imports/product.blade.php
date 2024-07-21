<form action="{{route('import.product.save')}}" method="post" enctype="multipart/form-data">
    @csrf
<input type="file" name="file" id="file">
<button type="submit">submit</button>
</form>