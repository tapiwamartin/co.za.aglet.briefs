
<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public int $page = 1;

    public function with()
    {
        $client = new \GuzzleHttp\Client();
        $allMovies = [];

        // Fetch first 3 pages (up to 60 movies)
        for ($page = 1; $page <= 3; $page++) {
            $response = $client->request('GET', 'https://api.themoviedb.org/3/account/22051016/favorite/movies?language=en-US&page=1&sort_by=created_at.asc', [
                'headers' => [
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkM2UyYWI1Mjk1ZjZjNDI4MjY1MmUwM2MyOGUzZTcwMSIsIm5iZiI6MTc0ODg2ODU1NS4wMzgsInN1YiI6IjY4M2Q5ZGNiNTBjYzQ3NjE2ZDdmNDQxNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.1zkz4tDZNbWVcYKlr_uUT9HExQeO3-sdBkLgpBsgGDE',
                    'accept' => 'application/json',
                ],
                'query' => [
                    'page' => $page,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $allMovies = array_merge($allMovies, $data['results']);
        }

        $allMovies = array_slice($allMovies, 0, 45); // Limit to 45

        $perPage = 9;
        $totalPages = ceil(count($allMovies) / $perPage);
        $currentPage = max(1, min($this->page, $totalPages));
        $offset = ($currentPage - 1) * $perPage;
        $movies = array_slice($allMovies, $offset, $perPage);

        return [
            'movies' => $movies,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ];
    }

    public function goToPage($page)
    {
        $this->page = $page;
    }

};
?>
<div>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Favourites</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($movies as $movie)
                <div class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center">
                    @if(isset($movie['poster_path']))
                        <img src="https://image.tmdb.org/t/p/w200{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="mb-2 rounded">
                    @endif
                    <h2 class="text-lg font-semibold mb-1 text-center">{{ $movie['title'] }}</h2>
                    <p class="text-gray-600 text-sm mb-2">ID: {{ $movie['id'] }}</p>
                    @if(isset($movie['release_date']))
                        <p class="text-gray-500 text-xs">Release: {{ $movie['release_date'] }}</p>
                    @endif
                    <button wire:click="requestSessionId({{$movie['id']}})" class="px-3 py-1 bg-gray-200 rounded">Add</button>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-center space-x-2">
            @if ($currentPage > 1)
                <button wire:click="goToPage({{ $currentPage - 1 }})" class="px-3 py-1 bg-gray-200 rounded">Previous</button>
            @endif
            <span>Page {{ $currentPage }} of {{ $totalPages }}</span>
            @if ($currentPage < $totalPages)
                <button wire:click="goToPage({{ $currentPage + 1 }})" class="px-3 py-1 bg-gray-200 rounded">Next</button>
            @endif
        </div>
    </div>
</div>
