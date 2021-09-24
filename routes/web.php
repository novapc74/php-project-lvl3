<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

Route::get('/', function (): string {
    return view('create');
})->name('urls.create');

Route::post('/urls', function (Request $request): object {
    $params = $request->all();
    $messages = [
        'required' => 'Поле не должно быть пустым',
        'url' => 'Некорректный URL',
        'max' => 'Длина URL не должна превышать 255 символов.',
    ];
    $validator = Validator::make($params['url'], [
        'name' => ['required', 'url', 'max:255'],
    ], $messages);
    if ($validator->fails()) {
        return redirect()
            ->route('urls.index')
            ->withErrors($validator)
            ->withInput();
    }
    $name = strtolower($params['url']['name']);
    $parsedName = parse_url($name);
    $name = $parsedName['scheme'] . '://' . $parsedName['host'];
    $dataUrls = DB::table('urls')->where('name', $name)->first();
    if (!is_null($dataUrls)) {
        session()->flash('status', 'Сайт обновлен');
        $created = $dataUrls->created_at;
        $id = $dataUrls->id;
    } else {
        session()->flash('status', 'Сайт добавлен');
        $created = now();
    }
    $updated = now();
    DB::table('urls')->updateOrInsert(
        ['name' => $name, 'created_at' => $created],
        ['updated_at' => $updated],
    );
    $id = DB::table('urls')->where('name', $name)->value('id');
    return redirect()->route('url.show', ['id' => $id]);
})->name('urls.store');

Route::get('/urls', function (): string {
    $urls = app('db')->table('urls')->orderBy('id')->paginate(15);
    $lastChecks = app('db')->table('url_checks')
        ->distinct('url_id')
        ->orderBy('url_id')
        ->latest()
        ->get()
        ->keyBy('url_id');
    $flash = session('status');
    return view('index', compact('urls', 'lastChecks', 'flash'));
})->name('urls.index');

Route::get('/urls/{id}', function ($id): string {
    $urlData = DB::table('urls')->where('id', $id)->first();
    if (is_null($urlData)) {
        abort(404);
    }
    $url = collect($urlData)->all();
    $urlCheck = DB::table('url_checks')->where('url_id', $id)->orderBy('created_at', 'desc')->get();
    $urlCheck = collect($urlCheck)->toArray();
    $flash = session('status');
    return view('url', compact('url', 'urlCheck', 'flash'));
})->name('url.show');

Route::post('/urls/{id}/checks', function ($id): object {
    $name = DB::table('urls')->where('id', $id)->value('name');
    $created = now();
    $errors = [];
    try {
        $response = Http::get($name);
    } catch (Throwable $e) {
        report($e);
        $errors['alert'] = $e->getMessage();
        return redirect()->route('url.show', ['id' => $id])->withErrors($errors)->withInput();
    }
    $statusCode = $response->status();
    $document = new Document($response->body());
    $h1 = trim(optional($document->first('h1'))->text());
    $keywords = optional($document->first('meta[name="keywords"]'))->getAttribute('content');
    $description = optional($document->first('meta[name="description"]'))->getAttribute('content');
    DB::table('url_checks')->insert(
        [
            'url_id' => $id,
            'status_code' => $statusCode,
            'h1' => $h1,
            'keywords' => $keywords,
            'description' => $description,
            'created_at' => $created
        ]
    );
    session()->flash('status', 'Страница успешно проверена');
    return redirect()->route('url.show', ['id' => $id]);
})->name('url.checks');
