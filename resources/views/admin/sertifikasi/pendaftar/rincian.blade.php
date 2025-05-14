<x-admin-layout>
    <div>
        <h5>{{ $asesi->student->user->name }}</h5>
        <div id="keputusan">
            <h4>Keputusan</h4>
            <form action="/updatestatus/{{ $asesi->id }}/{{ $asesi->sertification_id }}" method="POST">
                @csrf
                @method('PATCH') 
                <input type="hidden" name="status" value="dilanjutkan_asesmen">
                <button type="submit" class="cursor-pointer ">Asesi dapat melanjutkan ke pra asesmen</button>
            </form>

            <form action="/updatestatus/{{ $asesi->id }}/{{ $asesi->sertification_id }}" method="POST">
                @csrf
                @method('PATCH') 
                <input type="hidden" name="status" value="tidak_bisa_dilanjutkan">
                <button type="submit" class="cursor-pointer">Asesi tidak dapat melanjutkan</button>
            </form>


        </div>
    </div>
</x-admin-layout>
