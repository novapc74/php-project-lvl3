<nav class="navbar navbar-expand-md navbar-dark bg-dark">
<a class="navbar-brand" href="{{ route('root') }}">Анализатор страниц</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('root') }}">Главная</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="{{ route('urls.store') }}">Сайты</a>
            </li>
        </ul>
    </div>
</nav>
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        @foreach ($errors->all() as $message)
          {{ $message }}
        @endforeach
    </div>
@endif
@include('flash::message')
