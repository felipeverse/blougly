@extends('layout')

@section('title', ($title ?? 'Post') . ' — ' . $site['name'])

@section('content')
<article class="post">
    <h2>{{ $title }}</h2>
    @if (isset($tags) && count($tags))
        <div class="tags">
            @foreach ($tags as $tag)
                <span class="tag">{{ $tag }}</span>
            @endforeach
        </div>
    @endif
    <div class="body">{!! $body ?? '' !!}</div>
</article>