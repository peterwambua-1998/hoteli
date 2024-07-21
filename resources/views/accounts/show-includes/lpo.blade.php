<div class="mb-3" style="display: flex; flex-direction:row-reverse;">
    <a href="#" data-bs-toggle="modal" data-bs-target="#lpoModal"><button class="btn btn-outline-success">Add</button></a>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample4">
        <thead>
            <tr>
                <th>#</th>
                <th>File</th>
                <th>Date Uploaded</th>
                <th>Account</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($lpos as $lpo)
            <tr>
                <td>{{$number}}<?php $number++; ?></td>
                <td>{{$lpo->name}}</td>
                <td>{{$lpo->created_at}}</td>
                <td>{{$account->name}}</td>
                <td style="display: flex; gap: 20px;">
                    <a href="{{route('lpo.download', $lpo->id)}}">Download</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="lpoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('lpo.store')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{$account->id}}" name="account_id" />
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload lpo document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="exampleFormControlFile1">Lpo Document</label>
                        <br>
                        <input type="file" class="form-control-file" id="myDropify" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>