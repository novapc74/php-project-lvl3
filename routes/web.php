<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

Route::get('/', function (): string {
    return view('index');
})->name('urls.index');

Route::post('/urls', function (Request $request): object {
    $params = $request->all();
    $messages = [
        'required' => 'Некорректный URL',
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
    !isset(parse_url($name)['scheme']) ? $name = 'https://' . $name : '';
    $parsedName = parse_url($name);
    $name = $parsedName['scheme'] . '://' . $parsedName['host'];
    $dataUrls = DB::table('urls')->where('name', $name)->first();
    if ($dataUrls !== null) {
        session()->flash('status', 'сайт обновлен');
        $created_at = $dataUrls->created_at;
        $id = $dataUrls->id;
    } else {
        session()->flash('status', 'сайт добавлен');
        $created_at = Carbon::now();
    }
    $updated = Carbon::now();
    DB::table('urls')->updateOrInsert(
        ['name' => $name, 'created_at' => $created_at],
        ['updated_at' => $updated],
    );
    $id = DB::table('urls')->where('name', $name)->value('id');
    return redirect()->route('url.show', ['id' => $id]);
})->name('urls.store');

Route::get('/urls', function (Request $request): string {
    $latestPosts = DB::table('url_checks')
               ->select('url_id', 'status_code', DB::raw('MAX(updated_at) as last_post_updated_at'))
               ->groupBy('url_id', 'status_code');
    $urlsJoin = DB::table('urls')
        ->leftJoinSub($latestPosts, 'latest_posts', function ($join): void {
            $join->on('urls.id', '=', 'latest_posts.url_id');
        })->select('id', 'name', 'last_post_updated_at', 'status_code')->orderBy('created_at')->get();
    $urlsAll = collect($urlsJoin)->toArray();
    $page = isset($request->page) ? $request->page : 1;
    $perPage = 15;
    $offset = (int)(($page * $perPage) - $perPage);
    $urls =  new LengthAwarePaginator(
        array_slice($urlsAll, $offset, $perPage, true),
        count($urlsAll),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );
    $flash = session('status');
    return view('urls', ['urls' => $urls, 'flash' => $flash]);
})->name('urls.show');

Route::get('/urls/{id}', function ($id): string {
    $urlData = DB::table('urls')->where('id', $id)->first();
    !$urlData ? abort(404) : '';
    $url = collect($urlData)->all();
    $urlCheck = DB::table('url_checks')->where('url_id', $id)->orderBy('updated_at', 'desc')->get();
    $urlCheck = collect($urlCheck)->toArray();
    $flash = session('status');
    return view('url', ['url' => $url, 'urlCheck' => $urlCheck, 'flash' => $flash]);
})->name('url.show');

Route::post('/urls/{id}/checks', function ($id): object {
    $created = DB::table('url_checks')->where('url_id', $id)->value('created_at');
    $name = DB::table('urls')->where('id', $id)->value('name');
    $created ?? $created = Carbon::now();
    $updated = Carbon::now();
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
            'created_at' => $created,
            'updated_at' => $updated
        ]
    );
    session()->flash('status', 'Страница успешно проверена');
    return redirect()->route('url.show', ['id' => $id]);
})->name('url.check');
