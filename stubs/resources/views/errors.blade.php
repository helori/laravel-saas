@if($errors->any())
    <div class="alert alert-red mb-2 text-left">
        <ul class="list-disc pl-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
