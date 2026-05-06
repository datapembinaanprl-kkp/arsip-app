{{-- Recursive node: render satu member card + children-nya --}}
<div class="os-node" style="--depth: {{ $depth }};">

    <div class="os-node-card">
        <img
            src="{{ $member->photo_url }}"
            alt="{{ $member->name }}"
            class="os-node-photo"
        >
        <div class="os-node-info">
            <div class="os-node-name">{{ $member->name }}</div>
            <div class="os-node-position">{{ $member->position }}</div>
        </div>
        <div class="os-node-actions">
            <a href="{{ route('organizational-structure.edit', $member) }}"
               class="os-node-btn" title="Edit">✎</a>

            <form action="{{ route('organizational-structure.destroy', $member) }}"
                  method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="os-node-btn os-node-btn-danger"
                        title="Hapus"
                        onclick="if(confirm('Hapus {{ addslashes($member->name) }}?')) this.closest('form').submit()">
                    ✕
                </button>
            </form>
        </div>
    </div>

    {{-- Render children secara rekursif --}}
    @if($member->allChildren->isNotEmpty())
        <div class="os-children">
            @foreach($member->allChildren as $child)
                @include('organizational-structure.tree-node', [
                    'member' => $child,
                    'depth'  => $depth + 1,
                ])
            @endforeach
        </div>
    @endif

</div>