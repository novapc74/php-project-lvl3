<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('head')
    <body class="vh-100 d-flex flex-column">
        @include('nav')
        <main class="flex-grow-1">
            <div class="container-lg">
                <h1 class="mt-5 mb-3">Сайт: {{ $url['name'] }}</h1>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap">
                        <tr>
                        <td>{{ 'ID'}}</td>
                        <td>{{ $url['id'] }}</td>
                        </tr>
                        <tr>
                        <td>{{ 'Имя' }}</td>
                        <td>{{ $url['name'] }}</td>
                        </tr>
                        <tr>
                        <td>{{ 'Дата создания' }}</td>
                        <td>{{ $url['created_at'] }}</td>
                        </tr>
                        <tr>
                        <td>{{ 'Дата обновления' }}</td>
                        <td>{{ $url['updated_at'] }}</td>
                        </tr>
                    </table>
                </div>
                <h2 class="mt-5 mb-3">Проверки</h2>
                <p>
                <form action="/urls/{{ $url['id'] }}/checks" method="POST">
                    @csrf
                    <input type="hidden" name = "id" value = "{{ $url['id'] }}" />
                    <input type="submit" class="btn btn-primary" value="Запустить проверку">
                </form>
                </p>
                <table class="table table-bordered table-hover text-nowrap">
                    <tbody>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Код ответа</th>
                        <th scope="col">h1</th>
                        <th scope="col">keywords</th>
                        <th scope="col">description</th>
                        <th scope="col">Дата создания</th>
                    </tr>
                        <tr>
                            @foreach ($urlCheck as $url)
                                <tr>
                                    @foreach ($url as $dataView => $view)
                                        @if ($dataView !== 'url_id' && $dataView !== 'created_at')
                                            <td><?= $view ?></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tr>
                    </tbody>
                    </table>
        </div>
    </main>
    @include('footer')
    </body>
</html>
