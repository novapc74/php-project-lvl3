<nav class="navbar navbar-expand-md navbar-dark bg-dark">
<a class="navbar-brand" href="/">Анализатор страниц</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="/">Главная</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="/urls">Сайты</a>
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
@if (isset($flash))
<div class="alert alert-info" role="alert">
      {{ $flash }}
</div>
@endif
