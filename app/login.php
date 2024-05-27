<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .input-field {
            background-color: #e5e7eb;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
        }

        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .btn-primary {
            background-color: #6366f1;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
        }

        .btn-primary:hover {
            background-color: #4f46e5;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
            text-align: center;
        }

        .social-btn:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <div class="flex justify-center mb-4">
            <img src="https://via.placeholder.com/50" alt="Logo" class="h-12 w-12">
        </div>
        <h2 class="text-2xl font-bold text-center mb-4">Welcome back</h2>
        <p class="text-center mb-6">Please enter your details to sign in</p>
        <div class="flex space-x-2 mb-6">
            <button class="social-btn">
                <img src="https://via.placeholder.com/24" alt="Google" class="h-6 w-6">
            </button>
            <button class="social-btn">
                <img src="https://via.placeholder.com/24" alt="Apple" class="h-6 w-6">
            </button>
            <button class="social-btn">
                <img src="https://via.placeholder.com/24" alt="Facebook" class="h-6 w-6">
            </button>
        </div>
        <div class="flex items-center mb-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-2 text-gray-400">or</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>
        <form action="#" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <input type="email" id="email" name="email" class="input-field mt-1" placeholder="Enter your email">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="input-field mt-1" placeholder="••••••••">
                    <button type="button" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m4 4H9m-2 4H7m16-20a9.92 9.92 0 01-.68 3.68L4.07 4.07a9.92 9.92 0 01-.63-3.71h14.24zM4.2 4.2L19.8 19.8a9.92 9.92 0 01-15.6-15.6z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <input type="checkbox" id="remember" name="remember" class="mr-1">
                    <label for="remember" class="text-sm text-gray-700">Remember for 30 days</label>
                </div>
                <a href="#" class="text-sm text-blue-500">Forgot password?</a>
            </div>
            <button type="submit" class="w-full btn-primary">Sign in</button>
            <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? <a href="#"
                    class="text-blue-500">Create account</a></p>
        </form>
    </div>
</body>

</html>
