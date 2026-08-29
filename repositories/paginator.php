<?PHP
class Paginator{
  public int $currentPage;
  public int $perPage;
  public int $totalItems;
  public int $totalPages;
  public int $offset;

  public function __construct(int $currentPage, int $perPage,int $totalItems){
  $this->perPage= $perPage;
  $this->totalItems=$totalItems;

  $totalPages= (int) ceil($totalItems / $perPage);
  $this->totalPages= $totalPages;

  if($currentPage > $totalPages){
    $currentPage=$totalPages;
  }
  if($currentPage < 1){
    $currentPage = 1;
  }
  $this->currentPage =$currentPage;

  $this->offset= ($this->currentPage - 1) * $this->perPage;
}

}

?>