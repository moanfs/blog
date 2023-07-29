<div>
    <!-- <object data="{{$post->banner_url}}" type=""> -->
    <img class="object-cover object-center w-full h-64 rounded-lg lg:h-80" src="/images/default.jpg" alt="banner">
    <!-- </object> -->

    <div class="mt-8">

        <h1 class="mt-4 text-xl font-semibold text-gray-800 dark:text-white">
            {{$post->title}}
        </h1>

        <p class="mt-2 text-gray-500 dark:text-gray-400 line-clamp-3">
            {{$post->excerpt}}
        </p>

        <div class="flex items-center justify-between mt-4">
            <div>
                <!-- <a href="#" class="text-lg font-medium text-gray-700 dark:text-gray-300 hover:underline hover:text-gray-500">
                                    John snow
                                </a> -->
                <p class="text-sm text-gray-500 dark:text-gray-400">{{$post->created_at->diffForHumans()}}</p>
            </div>
            <a href="{{route('posts.show', $post->slug)}}" class="inline-block text-blue-500 underline hover:text-blue-400">Read more</a>
        </div>
    </div>
</div>