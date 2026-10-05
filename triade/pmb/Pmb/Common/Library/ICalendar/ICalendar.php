<?php

namespace Pmb\Common\Library\ICalendar;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class ICalendar
{
    /**
     * Retourne l'ical au format texte
     *
     * @var string
     */
    public const OUTPUT_DEST_S = 'S';

    /**
     * Envoi l'ical vers le navigateur
     *
     * @var string
     */
    public const OUTPUT_DEST_I = 'I';

    /**
     * Envoi l'ical vers un fichier local
     *
     * @var string
     */
    public const OUTPUT_DEST_F = 'F';

    /**
     * Éve?nement annulé
     *
     * @var int
     */
    public const STATUS_CANCELLED = 1;

    /**
     * Éve?nement confirme
     *
     * @var int
     */
    public const STATUS_CONFIRMED = 2;

    /**
     * Éve?nement en attente
     *
     * @var int
     */
    public const STATUS_TENTATIVE = 3;

    /**
     * Identifiant de l'éve?nement
     *
     * @var mixed
     */
    protected $uid;

    /**
     * Titre
     *
     * @var string
     */
    protected $title;

    /**
     * Description
     *
     * @var string
     */
    protected $summary;

    /**
     * Lieu
     *
     * @var string
     */
    protected $location;

    /**
     * Date de début
     *
     * @var \DateTime
     */
    protected $start;

    /**
     * Date de fin
     *
     * @var \DateTime
     */
    protected $end;

    /**
     * Statut
     *
     * @see self::STATUS_CANCELLED
     * @see self::STATUS_CONFIRMED
     * @see self::STATUS_TENTATIVE
     * @var int
     */
    protected $status;

    /**
     * L'ical
     *
     * @var string
     */
    private $ical = '';

    /**
     * Constructeur
     *
     * @param string $title
     * @param string $summary
     */
    public function __construct(string $title, string $summary)
    {
        $this->title = trim(strip_tags($title));
        $this->summary = trim(strip_tags($summary));
        $this->uid = md5($this->title . $this->summary);
    }

    /**
     * Ajoute les dates
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return void
     */
    public function setEvent(\DateTime $start, \DateTime $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Ajoute le lieu
     *
     * @param string $location
     * @return void
     */
    public function setLocation(string $location)
    {
        $this->location = trim(strip_tags($location));
    }

    /**
     * Ajoute le statut
     *
     * @param integer $status
     * @return void
     */
    public function setStatus(int $status)
    {
        if (!in_array($status, [self::STATUS_CANCELLED, self::STATUS_CONFIRMED, self::STATUS_TENTATIVE], true)) {
            throw new ICalendarException('Invalid status');
        }
        $this->status = $status;
    }

    /**
     * Genere l'ical
     *
     * @param boolean $unique
     * @return void
     */
    protected function generate(bool $unique = true)
    {
        if (!empty($this->ical)) {
            // L'ical a deja ete genere
            return;
        }

        if ($unique) {
            $this->ical = "BEGIN:VCALENDAR\n";
            $this->ical .= "VERSION:2.0\n";
            $this->ical .= "PRODID:-//PMB Services//PMB//FR\n";
        }

        $this->ical .= "BEGIN:VEVENT\n";
        $timezone = $this->start->getTimezone();
        $this->ical .= "DTSTART;TZID=" . $timezone->getName() . ":" . $this->start->format('Ymd\THis') . "\n";
        $this->ical .= "DTEND;TZID=" . $timezone->getName() . ":" . $this->end->format('Ymd\THis') . "\n";

        $this->ical .= "SEQUENCE:" . time() . "\n";
        $this->ical .= "METHOD:REQUEST\n";
        $this->ical .= "SUMMARY:" . $this->title . "\n";
        $this->ical .= "DESCRIPTION:" . $this->summary . "\n";

        if ($this->location) {
            $this->ical .= "LOCATION:" . $this->location . "\n";
        }

        if ($this->status) {
            switch ($this->status) {
                case self::STATUS_CANCELLED:
                    $this->ical .= "STATUS:CANCELLED\n";
                    break;
                case self::STATUS_CONFIRMED:
                    $this->ical .= "STATUS:CONFIRMED\n";
                    break;
                case self::STATUS_TENTATIVE:
                    $this->ical .= "STATUS:TENTATIVE\n";
                    break;
                default:
                    throw new ICalendarException('Invalid status');
            }
        }

        $this->ical .= "UID:" . $this->uid . "\n";
        $this->ical .= "END:VEVENT\n";
        if ($unique) {
            $this->ical .= "END:VCALENDAR\n";
        }
    }

    /**
     * Envoi l'ical
     *
     * @param string $dest (see self::OUTPUT_DEST_*)
     * @param string $outputFilename
     * @return void|string|bool
     * @throws ICalendarException
     */
    public function output(string $dest = self::OUTPUT_DEST_F, string $outputFilename = 'output.ics')
    {
        $this->generate();

        switch ($dest) {
            case self::OUTPUT_DEST_I:
                HEADER('Content-type: text/calendar');
                echo $this->ical;
                break;

            case self::OUTPUT_DEST_S:
                return $this->ical;

            case self::OUTPUT_DEST_F:
                $size = file_put_contents($outputFilename, $this->ical);
                return $size !== false ? $outputFilename : false;

            default:
                throw new ICalendarException('Invalid destination');
        }
    }

    /**
     * Genere l'ical
     *
     * @param ICalendar[] $events
     * @return string
     */
    protected static function generateEvents(array $events): string
    {
        $ical = "BEGIN:VCALENDAR\n";
        $ical .= "VERSION:2.0\n";
        $ical .= "PRODID:-//PMB Services//PMB//FR\n";

        foreach ($events as $event) {
            $event->generate(false);
            $ical .= $event->output(self::OUTPUT_DEST_S);
        }

        $ical .= "END:VCALENDAR\n";

        return $ical;
    }

    /**
     * Envoi l'ical
     *
     * @param ICalendar[] $events
     * @param string $dest (see self::OUTPUT_DEST_*)
     * @param string $outputFilename
     * @return void|string|bool
     * @throws ICalendarException
     */
    public static function outputEvents(array $events, string $dest = self::OUTPUT_DEST_F, string $outputFilename = 'output.ics')
    {
        $ical = static::generateEvents($events);

        switch ($dest) {
            case self::OUTPUT_DEST_I:
                HEADER('Content-type: text/calendar');
                echo $ical;
                break;

            case self::OUTPUT_DEST_S:
                return $ical;

            case self::OUTPUT_DEST_F:
                $size = file_put_contents($outputFilename, $ical);
                return $size !== false ? $outputFilename : false;

            default:
                throw new ICalendarException('Invalid destination');
        }
    }

    /**
     * Retourne l'ical
     *
     * @return string
     */
    public function __toString()
    {
        $this->generate();
        return $this->ical;
    }
}
