<?php

declare(strict_types=1);

namespace App\Store\Role\Rebac;

use App\Model\Role\Rebac\Tuple;
use App\Consistency\Role\Rebac\Token;
use PDO;
use Throwable;

/**
 *
 */

/**
 *
 */
final class PdoTupleStore implements TupleStoreInterface
{
    /**
     * @param \PDO $pdo
     */
    public function __construct(private readonly PDO $pdo) {}

    /**
     * @param string $ns
     * @param array $tuples
     * @return \App\Consistency\Role\Rebac\Token
     * @throws \Throwable
     */
    public function write(string $ns, array $tuples): Token
    {
        $this->pdo->beginTransaction();
        try {
            $ins = $this->pdo->prepare('INSERT INTO role_tuple(ns,obj_type,obj_id,relation,subj_type,subj_id,subj_rel) VALUES(?,?,?,?,?,?,?)');
            foreach ($tuples as $t) {
                $ins->execute([$ns, $t->objType, $t->objId, $t->relation, $t->subjType, $t->subjId, $t->subjRel]);
            }
            $this->bumpRev();
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return $this->currentToken();
    }

    /**
     * @param string $ns
     * @param \App\Model\Role\Rebac\Tuple $tuple
     * @return \App\Consistency\Role\Rebac\Token
     * @throws \Throwable
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare('DELETE FROM role_tuple WHERE ns=? AND obj_type=? AND obj_id=? AND relation=? AND subj_type=? AND subj_id=? AND (subj_rel IS ? OR subj_rel=?)');
            $del->execute([$ns, $tuple->objType, $tuple->objId, $tuple->relation, $tuple->subjType, $tuple->subjId, $tuple->subjRel, $tuple->subjRel]);
            $this->bumpRev();
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return $this->currentToken();
    }

    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     * @return iterable
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable
    {
        $sel = $this->pdo->prepare('SELECT ns,obj_type,obj_id,relation,subj_type,subj_id,subj_rel FROM role_tuple WHERE ns=? AND obj_type=? AND obj_id=? AND relation=?');
        $sel->execute([$ns, $objType, $objId, $relation]);
        while ($row = $sel->fetch(PDO::FETCH_ASSOC)) {
            yield Tuple::fromArray($row);
        }
    }

    /**
     * @param string $ns
     * @param string $subjType
     * @param string $subjId
     * @param string|null $subjRel
     * @return iterable
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable
    {
        $sel = $this->pdo->prepare('SELECT ns,obj_type,obj_id,relation,subj_type,subj_id,subj_rel FROM role_tuple WHERE ns=? AND subj_type=? AND subj_id=? AND (subj_rel IS ? OR subj_rel=?)');
        $sel->execute([$ns, $subjType, $subjId, $subjRel, $subjRel]);
        while ($row = $sel->fetch(PDO::FETCH_ASSOC)) {
            yield Tuple::fromArray($row);
        }
    }

    /**
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function currentToken(): Token
    {
        $rev = (int) $this->pdo->query('SELECT rev FROM role_rev WHERE id=1')->fetchColumn();
        return new Token($rev);
    }

    /**
     * @return void
     */
    private function bumpRev(): void
    {
        $this->pdo->exec('UPDATE role_rev SET rev = rev + 1 WHERE id=1');
    }
}
