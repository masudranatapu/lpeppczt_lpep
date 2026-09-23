@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 bg-white focus:border-red-500 focus:ring-red-500/20 rounded-md shadow-sm transition placeholder:text-slate-400 outline-none text-slate-900 text-sm py-2.5 ']) }}>

