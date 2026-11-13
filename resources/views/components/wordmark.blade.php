{{--
    The product wordmark with the mortarboard perched on the final "s".

    Every surface that prints the name — masthead, login hero, register hero —
    renders this component, so the hat is one implementation rather than three
    copies that drift. Geometry lives in .cap-sit/.cap (em based), so the hat
    scales with whatever font-size the surrounding heading sets and the accent
    follows the text colour it is dropped into.

    Usage:  <x-wordmark />        inside an <h1>
            Join <x-wordmark />  when the name is part of a longer heading
--}}
<span class="wordmark">IskolarNote<span class="cap-sit">s<span class="cap" aria-hidden="true"><x-icon name="graduation-cap" :size="24" /><span class="cap-knot"></span></span></span></span>
