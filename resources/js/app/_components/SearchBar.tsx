type SearchBarProps = {
  compact?: boolean;
};

export function SearchBar({ compact = false }: SearchBarProps) {
  return (
    <form className={compact ? "search-bar search-bar--compact" : "search-bar"} action="/products">
      <input name="q" type="search" placeholder="Ürün, marka veya kategori ara" aria-label="Ürün ara" />
      <button type="submit">Ara</button>
    </form>
  );
}
