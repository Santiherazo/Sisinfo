<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface - Tailwind CSS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="flex h-screen w-full">
  <div class="bg-gray-900 text-white p-6 flex flex-col gap-6">
    <a class="flex items-center gap-2" href="#">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="w-6 h-6"
      >
        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
        <path d="M3 9h18"></path>
        <path d="M3 15h18"></path>
        <path d="M9 3v18"></path>
        <path d="M15 3v18"></path>
      </svg>
      <span class="text-lg font-bold">Admin Panel</span>
    </a>
    <nav class="flex flex-col gap-2">
      <a class="flex items-center gap-2 hover:bg-gray-800 px-4 py-2 rounded" href="#">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="w-5 h-5"
        >
          <line x1="12" x2="12" y1="20" y2="10"></line>
          <line x1="18" x2="18" y1="20" y2="4"></line>
          <line x1="6" x2="6" y1="20" y2="16"></line>
        </svg>
        <span>Statistics</span>
      </a>
      <a class="flex items-center gap-2 hover:bg-gray-800 px-4 py-2 rounded" href="#">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="w-5 h-5"
        >
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <span>Users</span>
      </a>
      <a class="flex items-center gap-2 hover:bg-gray-800 px-4 py-2 rounded" href="#">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="w-5 h-5"
        >
          <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
          <rect width="20" height="14" x="2" y="6" rx="2"></rect>
        </svg>
        <span>Projects</span>
      </a>
      <a class="flex items-center gap-2 hover:bg-gray-800 px-4 py-2 rounded" href="#">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="w-5 h-5"
        >
          <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
          <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
        </svg>
        <span>PDF Report</span>
      </a>
      <a class="flex items-center gap-2 hover:bg-gray-800 px-4 py-2 rounded" href="#">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="w-5 h-5"
        >
          <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
        </svg>
        <span>Option A</span>
      </a>
    </nav>
  </div>
  <div class="flex-1 bg-gray-100 dark:bg-gray-950 p-8">
    <section id="statistics" class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Statistics</h2>
        <div class="flex gap-4">
          <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            Last 30 days
          </button>
          <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            Last 60 days
          </button>
          <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            Last 90 days
          </button>
        </div>
      </div>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight">Users</h3>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="w-6 h-6 text-gray-500 dark:text-gray-400"
            >
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold">2,345</div>
            <p class="text-sm text-gray-500 dark:text-gray-400">+15% from last month</p>
          </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight">Projects</h3>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="w-6 h-6 text-gray-500 dark:text-gray-400"
            >
              <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              <rect width="20" height="14" x="2" y="6" rx="2"></rect>
            </svg>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold">124</div>
            <p class="text-sm text-gray-500 dark:text-gray-400">+8% from last month</p>
          </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight">Won Projects</h3>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="w-6 h-6 text-gray-500 dark:text-gray-400"
            >
              <circle cx="12" cy="12" r="10"></circle>
              <path d="m9 12 2 2 4-4"></path>
            </svg>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold">82</div>
            <p class="text-sm text-gray-500 dark:text-gray-400">+12% from last month</p>
          </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight">Lost Projects</h3>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="w-6 h-6 text-gray-500 dark:text-gray-400"
            >
              <circle cx="12" cy="12" r="10"></circle>
              <path d="m15 9-6 6"></path>
              <path d="m9 9 6 6"></path>
            </svg>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold">42</div>
            <p class="text-sm text-gray-500 dark:text-gray-400">-5% from last month</p>
          </div>
        </div>
      </div>
      <div class="mt-8">
        <div class="w-full aspect-[16/9]">
          <div style="width: 100%; height: 100%;">
            <div style="position: relative;">
              <svg xmlns="http://www.w3.org/2000/svg" width="832.375" height="468.203125" role="application">
                <rect width="832.375" height="468.203125" fill="transparent"></rect>
                <g transform="translate(40,10)">
                  <g>
                    <line opacity="1" x1="0" x2="0" y1="0" y2="418.203125" stroke="#f3f4f6" stroke-width="1"></line>
                    <line
                      opacity="1"
                      x1="156.475"
                      x2="156.475"
                      y1="0"
                      y2="418.203125"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="312.95"
                      x2="312.95"
                      y1="0"
                      y2="418.203125"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="469.42499999999995"
                      x2="469.42499999999995"
                      y1="0"
                      y2="418.203125"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="625.9"
                      x2="625.9"
                      y1="0"
                      y2="418.203125"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="782.375"
                      x2="782.375"
                      y1="0"
                      y2="418.203125"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                  </g>
                  <g>
                    <line
                      opacity="1"
                      x1="0"
                      x2="782.375"
                      y1="418"
                      y2="418"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="0"
                      x2="782.375"
                      y1="316"
                      y2="316"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="0"
                      x2="782.375"
                      y1="213"
                      y2="213"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line
                      opacity="1"
                      x1="0"
                      x2="782.375"
                      y1="111"
                      y2="111"
                      stroke="#f3f4f6"
                      stroke-width="1"
                    ></line>
                    <line opacity="1" x1="0" x2="782.375" y1="8" y2="8" stroke="#f3f4f6" stroke-width="1"></line>
                  </g>
                  <g transform="translate(0,418.203125)">
                    <g transform="translate(0,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        Jan
                      </text>
                    </g>
                    <g transform="translate(156.475,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        Feb
                      </text>
                    </g>
                    <g transform="translate(312.95,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        Mar
                      </text>
                    </g>
                    <g transform="translate(469.42499999999995,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        Apr
                      </text>
                    </g>
                    <g transform="translate(625.9,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        May
                      </text>
                    </g>
                    <g transform="translate(782.375,0)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="text-before-edge"
                        text-anchor="middle"
                        transform="translate(0,16) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        Jun
                      </text>
                    </g>
                    <line x1="0" x2="782.375" y1="0" y2="0" style="stroke: transparent; stroke-width: 1;"></line>
                  </g>
                  <g transform="translate(0,0)">
                    <g transform="translate(0,418)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="central"
                        text-anchor="end"
                        transform="translate(-16,0) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        0
                      </text>
                    </g>
                    <g transform="translate(0,316)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="central"
                        text-anchor="end"
                        transform="translate(-16,0) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        50
                      </text>
                    </g>
                    <g transform="translate(0,213)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="central"
                        text-anchor="end"
                        transform="translate(-16,0) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        100
                      </text>
                    </g>
                    <g transform="translate(0,111)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="central"
                        text-anchor="end"
                        transform="translate(-16,0) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        150
                      </text>
                    </g>
                    <g transform="translate(0,8)" style="opacity: 1;">
                      <line x1="0" x2="0" y1="0" y2="0" style="stroke: rgb(119, 119, 119); stroke-width: 1;"></line>
                      <text
                        dominant-baseline="central"
                        text-anchor="end"
                        transform="translate(-16,0) rotate(0)"
                        style="font-family: sans-serif; font-size: 11px; fill: rgb(51, 51, 51);"
                      >
                        200
                      </text>
                    </g>
                    <line x1="0" x2="0" y1="0" y2="418.203125" style="stroke: transparent; stroke-width: 1;"></line>
                  </g>
                  <path
                    d="M0,295L156.475,320L312.95,55L469.425,258L625.9,221L782.375,0"
                    fill="none"
                    stroke-width="2"
                    stroke="#e11d48"
                  ></path>
                  <path
                    d="M0,330L156.475,137L312.95,293L469.425,121L625.9,365L782.375,103"
                    fill="none"
                    stroke-width="2"
                    stroke="#2563eb"
                  ></path>
                  <g>
                    <g transform="translate(782.375, 0)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(625.9, 221)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(469.42499999999995, 258)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(312.95, 55)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(156.475, 320)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(0, 295)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#e11d48"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(782.375, 103)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(625.9, 365)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(469.42499999999995, 121)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(312.95, 293)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(156.475, 137)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                    <g transform="translate(0, 330)" style="pointer-events: none;">
                      <circle
                        r="3"
                        fill="#2563eb"
                        stroke="transparent"
                        stroke-width="0"
                        style="pointer-events: none;"
                      ></circle>
                    </g>
                  </g>
                  <g transform="translate(0,0)">
                    <rect
                      data-ref="mesh-interceptor"
                      width="782.375"
                      height="418.203125"
                      fill="red"
                      opacity="0"
                      style="cursor: auto;"
                    ></rect>
                  </g>
                </g>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="users" class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Users</h2>
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Add User
        </button>
      </div>
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
        <div class="relative w-full overflow-auto">
          <table class="w-full caption-bottom text-sm">
            <thead class="[&amp;_tr]:border-b">
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Name
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Email
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Role
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="[&amp;_tr:last-child]:border-0">
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">John Doe</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">john@example.com</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Admin</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Jane Smith</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">jane@example.com</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Manager</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Bob Johnson</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">bob@example.com</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">User</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <section id="projects" class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Projects</h2>
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Add Project
        </button>
      </div>
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
        <div class="relative w-full overflow-auto">
          <table class="w-full caption-bottom text-sm">
            <thead class="[&amp;_tr]:border-b">
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Name
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Client
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Status
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Start Date
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  End Date
                </th>
                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="[&amp;_tr:last-child]:border-0">
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Website Redesign</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Acme Inc.</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div
                    class="inline-flex w-fit items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80"
                    color="green"
                  >
                    Won
                  </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-04-01</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-06-30</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Mobile App Development</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Globex Corp.</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div
                    class="inline-flex w-fit items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80"
                    color="red"
                  >
                    Lost
                  </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-02-15</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-05-31</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">ERP System Implementation</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">Stark</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div
                    class="inline-flex w-fit items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80"
                    color="green"
                  >
                    Won
                  </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">2023-</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                  <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path>
                        <line x1="18" x2="12" y1="9" y2="15"></line>
                        <line x1="12" x2="18" y1="9" y2="15"></line>
                      </svg>
                    </button>
                    <button
                      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                      color="red"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5"
                      >
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <section id="pdf" class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">PDF Report</h2>
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Generate PDF
        </button>
      </div>
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
        <div class="p-6">
          <p>
            This section allows you to generate a PDF report with the relevant information from the admin panel.
          </p>
          <p class="mt-4">The</p>
          <ul class="list-disc pl-6 mt-2">
            <li>Statistics (charts and key metrics)</li>
            <li>User management (list of users)</li>
            <li>Project management (list of projects)</li>
          </ul>
        </div>
      </div>
    </section>
    <section id="option-a" class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Option A</h2>
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
          Activate Option A
        </button>
      </div>
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
        <div class="p-6">
          <p>This is the content for Option A. It will include additional</p>
        </div>
      </div>
    </section>
  </div>
</div>
</body>