<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate\Service\Storage;


class Mongo implements StorageInterface
{
    const NUMBERS = 'numbers';
    const LOG     = 'log';

    /**
     * @var \MongoDB
     */
    protected $db;

    public function __construct(\MongoDB $db)
    {
        $this->db = $db;
    }

    public function set ($number, $source, $target)
    {
        $doc = [
            '_id' => $number,
            'source' => (string) $source,
            'target' => (string) $target,
            'modified' => new \DateTime()
        ];

        $this->db->selectCollection(self::NUMBERS)->save($doc);

        return $doc;
    }

    public function get ($number)
    {
        $doc = $this->db->selectCollection(self::NUMBERS)->findOne(['_id' => $number]);
        return $doc;
    }

    public function log ($number, $text, $translation)
    {
        $settings = $this->get($number);

        $doc = [
            'number' => $number,
            'request' => [
                'text' => $text,
                'translation' => $translation,
            ],
            'settings' => [
                'source' => $settings['source'],
                'target' => $settings['target'],
            ],
            'created' => new \MongoDate()
        ];

        $this->db->selectCollection(self::LOG)->save($doc);
        return $doc;
    }

    public function last ($number)
    {
        $cursor = $this->db->selectCollection(self::LOG)->find([
            'number' => $number
        ]);

        $cursor->sort(['created' => -1]);

        $last = $cursor->getNext();
error_log(var_export($last, true));
        return $last;
    }

    public function count ($number, \DateTime $start = null)
    {
        $end = clone $start;
        $end->add(new \DateInterval('P1D'));

        $start = new \MongoDate($start->getTimestamp());
        $end   = new \MongoDate($end->getTimestamp());

        $cursor = $this->db->selectCollection(self::LOG)->find([
            'number' => $number,
            'created' => [
                '$gte' => $start,
                '$lte' => $end
            ]
        ]);
error_log($cursor->count());
        return $cursor->count();
    }
}