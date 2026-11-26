@props(['href', 'active' => false, 'icon', 'label'])

<a href="{{ $href }}" class="nav-link sidebar-nav-link {{ $active ? 'active' : '' }}" title="{{ $label }}">
    <i class="bi {{ $icon }}"></i><span class="sidebar-nav-label">{{ $label }}</span>
</a>
