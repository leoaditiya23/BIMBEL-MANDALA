@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Kelas Saya</h2>
        <p class="text-sm text-slate-500">Kelola semua kelas yang Anda ajar</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($classes && count($classes) > 0)
            @foreach($classes as $class)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition cursor-pointer">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-black text-slate-800">{{ $class->name }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $class->jenjang ?? 'Program' }}</p>
                        </div>
                        <div class="bg-indigo-100 text-indigo-600 w-10 h-10 rounded-lg flex items-center justify-center text-sm font-black">
                            {{ $class->student_count ?? 0 }}
                        </div>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div class="flex items-center text-sm text-slate-600">
                            <i class="fas fa-users w-5"></i>
                            <span class="ml-2">{{ $class->student_count ?? 0 }} Siswa Aktif</span>
                        </div>
                        <div class="flex items-center text-sm text-slate-600">
                            <i class="fas fa-calendar-alt w-5"></i>
                            <span class="ml-2">{{ $class->schedule_count ?? 0 }} Sesi</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
                        <button class="flex-1 text-indigo-600 hover:text-indigo-800 font-bold text-sm">Lihat Detail</button>
                        <button class="flex-1 text-slate-600 hover:text-slate-800 font-bold text-sm">Edit</button>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold">Anda belum mengajar kelas apapun</p>
            </div>
        @endif
    </div>
</div>
@endsection
