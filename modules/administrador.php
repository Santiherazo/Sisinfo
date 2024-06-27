<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz de Usuario - Tailwind CSS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <button class="text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
                <nav class="ml-4">
                    <a href="#" class="text-gray-900 font-semibold">Users</a>
                    <a href="#" class="ml-4 text-gray-600">Projects</a>
                    <a href="#" class="ml-4 text-gray-600">Reports</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">
        <div class="flex space-x-4">
            <div class="bg-white rounded-lg shadow p-6 flex-1">
                <h2 class="text-xl font-semibold mb-2">John Doe</h2>
                <p class="text-gray-500">john@example.com</p>
                <p class="text-gray-600 mt-2">Admin</p>
                <p class="text-gray-400 mt-2">Joined 3 months ago</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-1">
                <h2 class="text-xl font-semibold mb-2">Jane Smith</h2>
                <p class="text-gray-500">jane@example.com</p>
                <p class="text-gray-600 mt-2">Manager</p>
                <p class="text-gray-400 mt-2">Joined 6 months ago</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-1">
                <h2 class="text-xl font-semibold mb-2">Michael Johnson</h2>
                <p class="text-gray-500">michael@example.com</p>
                <p class="text-gray-600 mt-2">User</p>
                <p class="text-gray-400 mt-2">Joined 1 year ago</p>
            </div>
        </div>
    </main>

</body>
</html>
