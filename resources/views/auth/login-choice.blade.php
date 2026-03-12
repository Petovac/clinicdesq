<x-guest-layout>
    <div class="max-w-md mx-auto text-center space-y-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Welcome to Clinicdesq
        </h1>

        <p class="text-gray-600">
            Please choose how you want to log in
        </p>

        <div class="space-y-4 mt-6">

            <a href="{{ url('/admin/login') }}"
               class="w-full inline-flex justify-center items-center
                      px-4 py-3 bg-gray-900 text-black rounded-lg
                      hover:bg-gray-800 transition">
                Admin Login
            </a>

            <a href="{{ url('/clinic/login') }}"
               class="w-full inline-flex justify-center items-center
                      px-4 py-3 bg-blue-600 text-black rounded-lg
                      hover:bg-blue-500 transition">
                Clinic Login
            </a>

            <a href="{{ url('/vet/login') }}"
               class="w-full inline-flex justify-center items-center
                      px-4 py-3 bg-green-600 text-black rounded-lg
                      hover:bg-green-500 transition">
                Vet Login
            </a>

        </div>

    </div>
</x-guest-layout>
