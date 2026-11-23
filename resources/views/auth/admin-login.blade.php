<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff - @bucket.imy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#FFF1F3] bg-[radial-gradient(circle_at_8%_10%,#FCE1E6_0,transparent_25%),radial-gradient(circle_at_92%_85%,#FCE1E6_0,transparent_25%)] px-4 py-10">

<main class="w-full max-w-[560px] rounded-[20px] bg-white px-5.5 pt-9 pb-7 text-center shadow-[0_10px_0_-6px_rgb(94_38_51/0.12),0_30px_60px_-30px_rgb(94_38_51/0.35)] sm:px-16 sm:pt-13 sm:pb-10">
    <p class="text-[15px] font-bold tracking-wide text-maroon">AREA STAFF</p>
    <h1 class="mt-1.5 mb-2 font-serif text-[34px] leading-tight font-medium sm:text-[44px]">Selamat Datang</h1>
    <p class="mb-5.5 text-[15px] text-muted">Pilih akses untuk masuk ke dashboard</p>

    <form method="POST" action="{{ route('admin.login.process') }}" class="flex flex-col gap-4.5 text-left" novalidate>
        @csrf

        <fieldset class="mb-1.5 grid grid-cols-2 gap-4">
            <legend class="sr-only">Masuk sebagai</legend>
            @foreach (['admin' => 'Admin', 'owner' => 'Owner'] as $value => $label)
                <label>
                    <input type="radio" name="role" value="{{ $value }}" @checked(old('role', 'admin') === $value) class="peer sr-only">
                    <span class="flex h-13.5 items-center justify-center rounded-[10px] border border-maroon bg-white text-[17px] text-maroon transition-colors hover:bg-blush peer-checked:bg-maroon peer-checked:text-white peer-focus-visible:ring-4 peer-focus-visible:ring-maroon/20">{{ $label }}</span>
                </label>
            @endforeach
        </fieldset>
        @error('role') <span class="field-error">{{ $message }}</span> @enderror

        <div class="flex flex-col gap-1">
            <label for="username" class="text-base">Username</label>
            <input id="username" name="username" type="text" placeholder="Username" value="{{ old('username') }}" autocomplete="username" required autofocus
                   @class(['field h-12 rounded-sm', 'border-danger' => $errors->has('username')])>
            @error('username') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-base">Password</label>
            <x-password id="password" placeholder="Password" autocomplete="current-password" required class="h-12 rounded-sm" />
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="mt-2.5 min-h-11.5 rounded-sm bg-maroon text-base text-white transition-colors hover:bg-maroon-dark">Masuk Dashboard</button>
    </form>

    <p class="mt-7.5 flex items-center gap-2 text-xs text-muted before:h-px before:flex-1 before:bg-ink after:h-px after:flex-1 after:bg-ink">
        <span>Hanya untuk Admin dan Owner bucket.imy</span>
    </p>
</main>

</body>
</html>
