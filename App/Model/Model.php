<?php
namespace App\Model;

use App\Core\Exception\DatabaseException;
use App\Core\Database;
use PDO;
use PDOException;
use PDOStatement;

class Model extends Database
{

  protected string $table;
  protected int $limit = 10;
  protected int $offset = 0;
  protected string $orderBy = "desc";
  protected string $orderColumn = "id";

  public function setOrderColumn(string $column)
  {
    $this->orderColumn = $column;
  }

  public function getOrderColumn()
  {
    return $this->orderColumn;
  }
  public function getLimit()
  {
    return $this->limit;
  }

  public function setLimit(int $limit)
  {
    $this->limit = $limit;
  }

  public function getOffset()
  {
    return $this->offset;
  }

  public function setOffset(int $offset)
  {
    $this->offset = $offset;
  }

  public function setOrderBy(string $nouvelOrdre)
  {
    $this->orderBy = $nouvelOrdre;
  }
  public function fetchAll(): array|null
  {

    try {
      $results = null;
      $pdo = $this->connect();
      $query = "SELECT * FROM $this->table
                ORDER BY $this->orderColumn $this->orderBy
                LIMIT $this->limit OFFSET $this->offset";

      $stm = $pdo->prepare($query);

      if ($stm->execute()) {
        while ($result = $stm->fetchObject($this::class)) {
          $results[] = $result;
        }
      }

      return $results;
    } catch (PDOException $e) {
      throw new DatabaseException("Error fetchAll data: " . $e->getMessage(), $e->getCode(), $e);
    }
  }

  public function count(array $where = []): int|null
  {
    $result = null;

    $pdo = $this->connect();
    $query = "SELECT COUNT(*) FROM $this->table";

    if (!empty($where)) {
      $keysValue = $this->setWhere($where);
      $query .= ' WHERE ' . $keysValue;
    }

    $stm = $pdo->prepare($query);

    if (!empty($where)) {
      $stm = $this->bindParams($stm, $where);
    }

    if ($stm->execute()) {
      $result = $stm->fetch();
    }

    return $result[0] != 0 ? $result[0] : null;
  }

  public function find($where, $where_not = []): array|null
  {
    try {
      $results = null;

      $keysValue = $this->setWhere($where, $where_not);

      $pdo = $this->connect();
      $query = "SELECT * FROM $this->table
                WHERE $keysValue
                ORDER BY $this->orderColumn $this->orderBy
                LIMIT $this->limit OFFSET $this->offset";

      $stm = $pdo->prepare($query);
      $stm = $this->bindParams($stm, $where, $where_not);

      if ($stm->execute()) {
        while ($result = $stm->fetchObject($this::class)) {
          $results[] = $result;
        }
      }

      return $results;
    } catch (PDOException $e) {
      throw new DatabaseException("Error find data: " . $e->getMessage(), $e->getCode(), $e);
    }
  }

  public function findOneBy($where, $where_not = []): object|null
  {
    try {
      $result = null;

      $keysValue = $this->setWhere($where, $where_not);
      $pdo = $this->connect();
      $query = "SELECT * FROM $this->table
                WHERE $keysValue
                ORDER BY $this->orderColumn $this->orderBy
                LIMIT $this->limit OFFSET $this->offset";

      $stm = $pdo->prepare($query);
      $stm = $this->bindParams($stm, $where, $where_not);

      if ($stm->execute()) {
        $result = $stm->fetchObject($this::class);
      }

      return is_bool($result) ? null : $result;
    } catch (PDOException $e) {
      throw new DatabaseException("Error findOneBy data: " . $e->getMessage(), $e->getCode(), $e);
    }
  }

  public function insert($data)
  {
    try {
      $keysValue = $this->setInsert($data);

      $pdo = $this->connect();
      $query = "INSERT INTO $this->table $keysValue";
      $stm = $pdo->prepare($query);
      $stm = $this->bindParams($stm, $data);
      $stm->execute();
    } catch (PDOException $e) {
      throw new DatabaseException("Error inserting data: " . $e->getMessage(), $e->getCode(), $e);
    }
  }
  public function update(array $data, int $id)
  {
    try {
      $keysValue = $this->setUpdate($data);
      $keysValueWhere = $this->setWhere(['id' => $id]);
      $pdo = $this->connect();
      $query = "UPDATE  $this->table SET $keysValue WHERE $keysValueWhere ";

      $stm = $pdo->prepare($query);
      $stm = $this->bindParams($stm, $data);
      $stm = $this->bindParams($stm, ['id' => $id]);
      $stm->execute();

    } catch (PDOException $e) {
      throw new DatabaseException("Error Updating data: " . $e->getMessage(), $e->getCode(), $e);
    }
  }

  public function delete(array $where)
  {
    try {
      $keysValue = $this->setWhere($where);

      $pdo = $this->connect();
      $query = "DELETE FROM $this->table WHERE $keysValue";
      $stm = $pdo->prepare($query);
      $stm = $this->bindParams($stm, $where);
      $stm->execute();

    } catch (PDOException $e) {
      throw new DatabaseException("Error delete data: " . $e->getMessage(), $e->getCode(), $e);
    }

  }

  private function setParamTypeForPDO($input)
  {
    if (is_bool($input)) {
      return PDO::PARAM_BOOL;

    } elseif (is_int($input)) {
      return PDO::PARAM_INT;

    } elseif (is_string($input)) {
      return PDO::PARAM_STR;
    }

  }

  private function bindParams($stm, $where, $where_not = []): PDOStatement
  {
    foreach (array_keys($where) as $key) {
      $stm->bindParam($key, $where[$key], $this->setParamTypeForPDO($where[$key]) ?? PDo::PARAM_NULL);
    }
    foreach (array_keys($where_not) as $key) {
      $stm->bindParam($key, $where_not[$key], $this->setParamTypeForPDO($where_not[$key]) ?? PDo::PARAM_NULL);
    }

    return $stm;
  }

  private function setWhere($where, $whereNot = []): string
  {
    $keysValue = '';

    foreach (array_keys($where) as $key) {
      $keysValue .= "$key = :$key AND ";
    }
    foreach (array_keys($whereNot) as $key) {
      $keysValue .= "$key != :$key AND ";
    }
    $keysValue = rtrim($keysValue, ' AND ');
    return $keysValue;
  }

  private function setInsert($data): string
  {
    $insertKeys = '(';

    foreach (array_keys($data) as $key) {
      $insertKeys .= "$key , ";
    }
    $insertKeys = rtrim($insertKeys, ' , ');

    $insertKeys .= ') VALUES (';
    foreach (array_keys($data) as $key) {
      $insertKeys .= ":$key , ";
    }

    $insertKeys = rtrim($insertKeys, ' , ');
    $insertKeys .= ')';

    return $insertKeys;
  }

  private function setUpdate($data)
  {
    $keysValue = '';

    foreach (array_keys($data) as $key) {
      $keysValue .= "$key = :$key , ";
    }

    $keysValue = rtrim($keysValue, ' , ');

    return $keysValue;
  }
}
