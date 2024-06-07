<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnH2lKj8tOgi0GsN+LgGeCOU82a6m2I1vKE6RnG/mwaUenlGg1dwzflsj4/m3Q+fYP+hv7Pmw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <button id="users-tab" class="text-black font-bold border-b-2 border-black focus:outline-none">Users</button>
                <button id="projects-tab" class="text-gray-600 hover:text-black focus:outline-none">Projects</button>
                <button id="reports-tab" class="text-gray-600 hover:text-black focus:outline-none">Reports</button>
            </div>
            <button id="add-button" class="bg-blue-500 text-white px-4 py-2 rounded-md">Add</button>
        </div>
    </nav>
    <div class="container mx-auto mt-6">
        <div id="users-section" class="grid grid-cols-3 gap-6">
            <!-- User Cards -->
            <!-- Repeat User Cards as needed -->
            <div class="bg-white p-4 rounded-lg shadow-md relative">
                <div class="flex justify-between">
                    <div>
                        <div class="text-lg font-bold">John Doe</div>
                        <div class="text-gray-600">john@example.com</div>
                        <div class="text-gray-800 font-semibold">Admin</div>
                        <div class="text-gray-500">Joined 3 months ago</div>
                    </div>
                    <div class="relative">
                        <button id="dropdownButton1" class="bg-gray-200 p-2 rounded-md">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdownMenu1" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg hidden">
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200 edit-user">Edit</a>
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add more user cards as needed -->
        </div>
        <div id="projects-section" class="grid grid-cols-3 gap-6 hidden">
            <!-- Project Cards -->
            <div class="bg-white p-4 rounded-lg shadow-md relative">
                <div class="flex justify-between">
                    <div>
                        <div class="text-lg font-bold">Project A</div>
                        <div class="text-gray-600">Website Redesign</div>
                    </div>
                    <div class="relative">
                        <button id="dropdownButton4" class="bg-gray-200 p-2 rounded-md">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdownMenu4" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg hidden">
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200 edit-project">Edit</a>
                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="text-gray-800 font-semibold">Approved</div>
                <div class="text-gray-500">Created 2 months ago</div>
                <div class="text-gray-500">Assigned to John Doe</div>
            </div>
            <!-- Add more project cards as needed -->
        </div>
        <div id="reports-section" class="grid grid-cols-2 gap-6 hidden">
            <!-- Report Cards (Placeholders) -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="text-lg font-bold">User Report</div>
                <div class="text-gray-600">Summary of all users</div>
                <div class="text-gray-800 font-semibold">Total Users: 45</div>
                <div class="text-gray-500">Admins: 5</div>
                <div class="text-gray-500">Managers: 10</div>
                <div class="text-gray-500">Users: 30</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="text-lg font-bold">Project Report</div>
                <div class="text-gray-600">Summary of all projects</div>
                <div class="text-gray-800 font-semibold">Total Projects: 25</div>
                <div class="text-gray-500">Approved: 18</div>
                <div class="text-gray-500">Rejected: 7</div>
            </div>
        </div>
    </div>

    <!-- User Form Modal -->
    <div id="user-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-md w-1/2">
            <h2 class="text-xl font-bold mb-4" id="user-modal-title">Create User</h2>
            <form id="user-form" class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="fullName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="institution" class="block text-sm font-medium text-gray-700">Institution</label>
                    <input type="text" id="institution" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="identification" class="block text-sm font-medium text-gray-700">Identification</label>
                    <input type="text" id="identification" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" id="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="idCard" class="block text-sm font-medium text-gray-700">ID Card</label>
                    <input type="text" id="idCard" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="stateProvince" class="block text-sm font-medium text-gray-700">State/Province</label>
                    <input type="text" id="stateProvince" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                    <select id="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select country</option>
                        <!-- Add more country options as needed -->
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select role</option>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </form>
            <div class="mt-4 flex justify-end space-x-2">
                <button id="cancel-user" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                <button id="save-user" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save</button>
            </div>
        </div>
    </div>

    <!-- Project Form Modal -->
    <div id="project-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-md w-1/2">
            <h2 class="text-xl font-bold mb-4" id="project-modal-title">Create Project</h2>
            <form id="project-form" class="grid grid-cols-2 gap-4">
                <div>
                    <label for="projectName" class="block text-sm font-medium text-gray-700">Project Name</label>
                    <input type="text" id="projectName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="projectType" class="block text-sm font-medium text-gray-700">Project Type</label>
                    <select id="projectType" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select project type</option>
                        <!-- Add more project type options as needed -->
                    </select>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div>
                    <label for="responsible" class="block text-sm font-medium text-gray-700">Responsible</label>
                    <input type="text" id="responsible" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select status</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="startDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="endDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </form>
            <div class="mt-4 flex justify-end space-x-2">
                <button id="cancel-project" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                <button id="save-project" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save</button>
            </div>
        </div>
    </div>

    <script>
        // Tab navigation
        document.getElementById('users-tab').addEventListener('click', function () {
            document.getElementById('users-section').classList.remove('hidden');
            document.getElementById('projects-section').classList.add('hidden');
            document.getElementById('reports-section').classList.add('hidden');
            this.classList.add('border-black', 'text-black');
            document.getElementById('projects-tab').classList.remove('border-black', 'text-black');
            document.getElementById('reports-tab').classList.remove('border-black', 'text-black');
        });

        document.getElementById('projects-tab').addEventListener('click', function () {
            document.getElementById('users-section').classList.add('hidden');
            document.getElementById('projects-section').classList.remove('hidden');
            document.getElementById('reports-section').classList.add('hidden');
            this.classList.add('border-black', 'text-black');
            document.getElementById('users-tab').classList.remove('border-black', 'text-black');
            document.getElementById('reports-tab').classList.remove('border-black', 'text-black');
        });

        document.getElementById('reports-tab').addEventListener('click', function () {
            document.getElementById('users-section').classList.add('hidden');
            document.getElementById('projects-section').classList.add('hidden');
            document.getElementById('reports-section').classList.remove('hidden');
            this.classList.add('border-black', 'text-black');
            document.getElementById('users-tab').classList.remove('border-black', 'text-black');
            document.getElementById('projects-tab').classList.remove('border-black', 'text-black');
        });

        // Add button event
        document.getElementById('add-button').addEventListener('click', function () {
            const activeTab = document.querySelector('nav button.border-black');
            if (activeTab.id === 'users-tab') {
                document.getElementById('user-modal').classList.remove('hidden');
            } else if (activeTab.id === 'projects-tab') {
                document.getElementById('project-modal').classList.remove('hidden');
            }
        });

        // User form modal events
        document.getElementById('cancel-user').addEventListener('click', function () {
            document.getElementById('user-modal').classList.add('hidden');
        });

        document.getElementById('save-user').addEventListener('click', function () {
            // Save user logic here
            document.getElementById('user-modal').classList.add('hidden');
        });

        // Project form modal events
        document.getElementById('cancel-project').addEventListener('click', function () {
            document.getElementById('project-modal').classList.add('hidden');
        });

        document.getElementById('save-project').addEventListener('click', function () {
            // Save project logic here
            document.getElementById('project-modal').classList.add('hidden');
        });

        // Dropdown menus
        document.querySelectorAll('[id^="dropdownButton"]').forEach(button => {
            button.addEventListener('click', function () {
                const menuId = this.id.replace('Button', 'Menu');
                document.getElementById(menuId).classList.toggle('hidden');
            });
        });

        // Edit user
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('user-modal-title').innerText = 'Edit User';
                document.getElementById('user-modal').classList.remove('hidden');
            });
        });

        // Edit project
        document.querySelectorAll('.edit-project').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('project-modal-title').innerText = 'Edit Project';
                document.getElementById('project-modal').classList.remove('hidden');
            });
        });
    </script>
</body>
</html>