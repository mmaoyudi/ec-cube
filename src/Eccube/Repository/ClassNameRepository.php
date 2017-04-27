<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Repository;

/**
 * ClassNameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClassNameRepository extends AbstractRepository
{
    /**
     * 規格一覧を取得する.
     *
     * @return array 規格の配列
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('cn')
            ->orderBy('cn.rank', 'DESC');
        $ClassNames = $qb->getQuery()
            ->getResult();

        return $ClassNames;
    }

    /**
     * 規格の順位を1上げる.
     *
     * @param  \Eccube\Entity\ClassName $ClassName
     * @return boolean 成功した場合 true
     */
    public function up(\Eccube\Entity\ClassName $ClassName)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $rank = $ClassName->getRank();

            //
            $ClassName2 = $this->findOneBy(array('rank' => $rank + 1));
            if (!$ClassName2) {
                throw new \Exception();
            }
            $ClassName2->setRank($rank);
            $em->persist($ClassName);

            // ClassName更新
            $ClassName->setRank($rank + 1);

            $em->persist($ClassName);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }

    /**
     * 規格の順位を1下げる.
     *
     * @param \Eccube\Entity\ClassName $ClassName
     * @return boolean 成功した場合 true
     */
    public function down(\Eccube\Entity\ClassName $ClassName)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $rank = $ClassName->getRank();

            //
            $ClassName2 = $this->findOneBy(array('rank' => $rank - 1));
            if (!$ClassName2) {
                throw new \Exception();
            }
            $ClassName2->setRank($rank);
            $em->persist($ClassName);

            // ClassName更新
            $ClassName->setRank($rank - 1);

            $em->persist($ClassName);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }

    /**
     * 規格を保存する.
     *
     * @param \Eccube\Entity\ClassName $ClassName
     * @return boolean 成功した場合 true
     */
    public function save($ClassName)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            if (!$ClassName->getId()) {
                $rank = $this->createQueryBuilder('cn')
                    ->select('MAX(cn.rank)')
                    ->getQuery()
                    ->getSingleScalarResult();
                if (!$rank) {
                    $rank = 0;
                }
                $ClassName->setRank($rank + 1);
                $ClassName->setDelFlg(0);
            }

            $em->persist($ClassName);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }

    /**
     * 規格を削除する.
     *
     * @param \Eccube\Entity\ClassName $ClassName
     * @return boolean 成功した場合 true
     */
    public function delete($ClassName)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            if ($ClassName->getClassCategories()->count() > 0) {
                throw new \Exception();
            }

            $rank = $ClassName->getRank();
            $em->createQueryBuilder()
                ->update('Eccube\Entity\ClassName', 'cn')
                ->set('cn.rank', 'cn.rank - 1')
                ->where('cn.rank > :rank')->setParameter('rank', $rank)
                ->getQuery()
                ->execute();

            $ClassName->setDelFlg(1);
            $em->persist($ClassName);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }

        return true;
    }
}
