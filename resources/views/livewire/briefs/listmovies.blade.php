
<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public int $page = 1;
    public string $search = '';
    public array $selectedMovie = [];
    public bool $showModal = false;

    public function with()
    {
        $client = new \GuzzleHttp\Client();
        $allMovies = [];

        if ($this->search) {
            $allMovies = array_filter($allMovies, function ($movie) {
                return stripos($movie['title'], $this->search) !== false;
            });

        }

        for ($page = 1; $page <= 3; $page++) {
            $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
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

        if ($this->search) {
            $allMovies = array_filter($allMovies, function ($movie) {
                return stripos($movie['title'], $this->search) !== false;
            });
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

   public function addFavorite($movieId)
   {

      $client = new \GuzzleHttp\Client();
       $response = $client->request('GET', 'https://api.themoviedb.org/3/authentication/token/new', [
           'headers' => [
               'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkM2UyYWI1Mjk1ZjZjNDI4MjY1MmUwM2MyOGUzZTcwMSIsIm5iZiI6MTc0ODg2ODU1NS4wMzgsInN1YiI6IjY4M2Q5ZGNiNTBjYzQ3NjE2ZDdmNDQxNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.1zkz4tDZNbWVcYKlr_uUT9HExQeO3-sdBkLgpBsgGDE',
               'accept' => 'application/json',
           ],
       ]);
      $data = json_decode($response->getBody(), true);
      $requestToken = $data['request_token'];
      //dd($requestToken);


      // redirect('https://www.themoviedb.org/authenticate/' . $requestToken . '?redirect_to=' . urlencode('https://localhost:8000/tmdb/callback'));


       $client = new \GuzzleHttp\Client();

       $response = $client->request('POST', 'https://api.themoviedb.org/3/authentication/token/validate_with_login', [
           'body' => json_encode([
               'username' => 'tapiwamartin',
               'password' => 'jR3QJgn.D8F2M#h',
               'request_token' => $requestToken,
           ]),           'headers' => [
               'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkM2UyYWI1Mjk1ZjZjNDI4MjY1MmUwM2MyOGUzZTcwMSIsIm5iZiI6MTc0ODg2ODU1NS4wMzgsInN1YiI6IjY4M2Q5ZGNiNTBjYzQ3NjE2ZDdmNDQxNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.1zkz4tDZNbWVcYKlr_uUT9HExQeO3-sdBkLgpBsgGDE',
               'accept' => 'application/json',
               'content-type' => 'application/json',
           ],
       ]);
       $response = $client->request('POST', 'https://api.themoviedb.org/3/authentication/session/new', [
           'headers' => [
               'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkM2UyYWI1Mjk1ZjZjNDI4MjY1MmUwM2MyOGUzZTcwMSIsIm5iZiI6MTc0ODg2ODU1NS4wMzgsInN1YiI6IjY4M2Q5ZGNiNTBjYzQ3NjE2ZDdmNDQxNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.1zkz4tDZNbWVcYKlr_uUT9HExQeO3-sdBkLgpBsgGDE',
               'accept' => 'application/json',
               'content-type' => 'application/json',
           ],
           'body' => json_encode([
               'request_token' => $requestToken,
           ]),
       ]);
       $sessionId = json_decode($response->getBody(), true)['session_id'];

       $client = new \GuzzleHttp\Client();
       $response = $client->post("https://api.themoviedb.org/3/account/22051016/favorite", [
           'headers' => [
               'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkM2UyYWI1Mjk1ZjZjNDI4MjY1MmUwM2MyOGUzZTcwMSIsIm5iZiI6MTc0ODg2ODU1NS4wMzgsInN1YiI6IjY4M2Q5ZGNiNTBjYzQ3NjE2ZDdmNDQxNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.1zkz4tDZNbWVcYKlr_uUT9HExQeO3-sdBkLgpBsgGDE',
               'accept' => 'application/json',
           ],
           'query' => [
               'session_id' => $sessionId,
           ],
           'json' => [
               'media_type' => 'movie',
               'media_id' => $movieId,
               'favorite' => true,
           ],
       ]);

       session()->flash('success', 'Movie added to favorites!');
       //dd(json_decode($response->getBody(), true));
   }

// logic for viewing more info about a movie
    public function showMovie($movie)
    {
        $this->selectedMovie = $movie;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

};
?>
<div>
   <div class="mb-4 mt-6 w-1/2 mx-auto">
        <input
            type="text"
            wire:model.live.debounce.50ms="search"
            placeholder="Search movies..."
            class="border rounded px-3 py-1 w-full"
        />
    </div>
    @if (session()->has('success'))
        <div class="text-green-600 text-center mb-4">{{ session('success') }}</div>
    @endif
    <div class="p-6">
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
                        <button wire:click="addFavorite({{$movie['id']}})" class="px-3 py-1 bg-gray-200 rounded">Add to Favorites</button>
{{--
                        <button wire:click="showMovie(@json($movie))" class="px-3 py-1 bg-blue-200 rounded mb-2">View More Info</button>
--}}

                        <!-- Modal -->
                        @if($showModal)
                            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                <div class="bg-white p-6 rounded shadow-lg w-96">
                                    <h2 class="text-xl font-bold mb-2">{{ $selectedMovie['title'] ?? '' }}</h2>
                                    <p class="mb-2">{{ $selectedMovie['overview'] ?? 'No description.' }}</p>
                                    <p class="text-sm text-gray-500 mb-2">Release: {{ $selectedMovie['release_date'] ?? '' }}</p>
                                    <button wire:click="closeModal" class="mt-4 px-4 py-2 bg-gray-300 rounded">Close</button>
                                </div>
                            </div>
                        @endif
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
