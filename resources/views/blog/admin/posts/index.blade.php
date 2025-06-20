@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @include('blog.admin.posts.includes.result_messages')
                <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                    <a href="{{ route('blog.admin.posts.create') }}" class="btn btn-primary">Додати</a>
                </nav>
                <div class="card mt-3">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Автор</th>
                                <th>Категорія</th>
                                <th>Заголовок</th>
                                <th>Дата публікації</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($paginator as $post)
                                @php /** @var \App\Models\BlogPost $post */ @endphp
                                <tr @if (!$post->is_published) style="background-color: #ccc;" @endif>
                                    <td>{{ $post->id }}</td>
                                    <td>{{ $post->user->name }}</td>    {{-- виводимо ім'я користувача і назву категорії зі зв'язаних таблиць --}}
                                    <td>{{ $post->category->title }}</td>
                                    <td>
                                        <a href="{{ route('blog.admin.posts.edit', $post->id) }}">
                                            {{ $post->title }}
                                        </a>
                                    </td>
                                    <td>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d.M H:i') : '' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($paginator->total() > $paginator->count())
                    <div class="mt-3">
                        {{ $paginator->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
