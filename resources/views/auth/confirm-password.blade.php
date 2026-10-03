<x-guest-layout>
    <p class="text-muted small mb-3">
        This is a secure area. Please confirm your password before continuing.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password" autofocus>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
    </form>
</x-guest-layout>
