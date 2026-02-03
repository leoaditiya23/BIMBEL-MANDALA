@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8">
    <div x-data="{ showMentorModal: false }">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800">Manajemen Mentor</h2>
                <p class="text-sm text-slate-500">Kelola semua pengajar yang aktif</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            @if($mentors && count($mentors) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($mentors as $mentor)
                        <div class="border border-slate-100 rounded-2xl p-6 hover:shadow-lg transition">
                            <div class="flex items-center mb-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=f97316&color=fff" 
                                     class="w-12 h-12 rounded-lg border-2 border-orange-100">
                                <div class="ml-4 flex-1">
                                    <p class="font-black text-slate-800">{{ $mentor->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $mentor->email }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="text-slate-600"><strong>Spesialisasi:</strong> {{ $mentor->specialization ?? '-' }}</p>
                                <p class="text-slate-600"><strong>Program:</strong> {{ $mentor->program_count ?? 0 }} Program</p>
                            </div>
                            <button class="mt-4 w-full text-blue-600 hover:text-blue-800 font-bold text-sm">Lihat Detail</button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                    <p class="text-slate-500 font-semibold">Belum ada mentor</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection