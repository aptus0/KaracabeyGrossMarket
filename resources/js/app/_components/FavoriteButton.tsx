type FavoriteButtonProps = {
  productSlug: string;
};

export function FavoriteButton({ productSlug }: FavoriteButtonProps) {
  return (
    <form action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/favorites/${productSlug}`} method="post">
      <button className="icon-button" type="submit" aria-label="Favorilere ekle" title="Favorilere ekle">
        ♡
      </button>
    </form>
  );
}
