<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('head')
    <body class="vh-100 d-flex flex-column">
        @include('nav')
        <main class="flex-grow-1">
            <div class="container-lg">
                <h1 class="mt-5 mb-3">Сайты</h1>
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap">
                        <tbody>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Последняя проверка</th>
                                <th scope="col">Код ответа</th>
                            </tr>
                            @foreach ($urls as $url)
                            <tr>
                                <th scope="row">{{ $url->id }}</th>
                                <td><a href="/urls/{{ $url->id }}">{{ $url->name }}</a></td>
                                <td>{{ $url->last_post_updated_at }}</td>
                                <td>{{ $url->status_code }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <nav aria-label="navigation">
                    {{ $urls->links("pagination::bootstrap-4") }}
                </nav>
            </div>
        </main>
    @include('footer')
    </body>
</html>
