<?php

namespace App\Services\Subject;

use App\Services\ApiLinker;
use App\Services\Checkers\UserChecker;
use App\Services\ImageManager;
use Doctrine\Persistence\ManagerRegistry;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SubjectService
{

    # --------------------------------
    # Attributes

    protected ApiLinker $apiLinker;
    protected ImageManager $manager;

    # --------------------------------
    # Constructor

    /**
     * SignService constructor.
     *
     */
    public function __construct(ApiLinker $apiLinker, ImageManager $manager)
    {
        $this->apiLinker = $apiLinker;
        $this->manager = $manager;
    }

    # --------------------------------
    # Core methods

    /**
     * Get user profil picture
     *
     * @param string $imageName
     * @return mixed
     */
    public function getProfilPic(string $imageName)
    {
        $pathImage = $this->manager->getUserImage($imageName);

        return $pathImage;
    }

    /**
     * Sort an array by a criteria
     *
     * @param $criteria
     * @return array
     */
    public function sortSubjectsByCriteria($criteria) {
        if ($_GET["sortCriteria"] == "mostRecents") {

            $selectedOption = "mostRecents";

            $dataToSend = json_encode(array(
                "criteria" => "mostRecents"
            ));
            $subjects = json_decode($subjects = $this->apiLinker->readData("subject", $dataToSend), true);
        }
        elseif ($_GET["sortCriteria"] == "oldests") {

            $selectedOption = "oldests";

            $dataToSend = json_encode(array(
                "criteria" => "oldests"
            ));
            $subjects = json_decode($this->apiLinker->readData("subject", $dataToSend), true);
        }
        elseif ($_GET["sortCriteria"] == "mostComments") {

            $selectedOption = "mostComments";

            $dataToSend = json_encode(array(
                "criteria" => "mostComments"
            ));
            $subjects = json_decode($this->apiLinker->readData("subject", $dataToSend), true);
        }
        elseif ($_GET["sortCriteria"] == "leastComments") {

            $selectedOption = "leastComments";

            $dataToSend = json_encode(array(
                "criteria" => "leastComments"
            ));
            $subjects = json_decode($this->apiLinker->readData("subject", $dataToSend), true);
        }

        return array("subjects" => $subjects,
            "selectedOption" => $selectedOption
        );
    }

    /**
     * Return a list of subjects that match the title given
     *
     * @param $title
     * @return array
     */
    public function searchSubjectsByName($title) {

        return json_decode($this->apiLinker->readData("subject/search/".$title), true);
    }

    /**
     * Close a subject
     *
     * @param $user
     * @param $subject
     * @return mixed
     */
    public function closeSubject($user, $subject) {

        // Check if user that make the request is admin
        if ($user["isAdmin"] == true) {

            $arrayDataToSend = array(
                "title" => $subject["title"],
                "description" => $subject["description"],
                "is_closed" => true
            );
            $dataToSend = json_encode($arrayDataToSend);

            // Update the subject
            $this->apiLinker->updateData("subject/".$subject["id"], $dataToSend);
        }
    }

    /**
     * open a subject
     *
     * @param $user
     * @param $subject
     * @return mixed
     */
    public function openSubject($user, $subject) {

        // Check if user that make the request is admin
        if ($user["isAdmin"] == true) {

            $arrayDataToSend = array(
                "title" => $subject["title"],
                "description" => $subject["description"],
                "is_closed" => false
            );
            $dataToSend = json_encode($arrayDataToSend);

            // Update the subject
            $this->apiLinker->updateData("subject/".$subject["id"], $dataToSend);
        }
    }

    /**
     * Delete a subject
     *
     * @param $user
     * @param $subject
     * @return mixed
     */
    public function deleteSubject($user, $subject) {

        // Check if user that make the request is admin or the author of the subject
        if ($user["isAdmin"] == true || $user["id"] == $subject["user"]["id"]) {

            // Delete the subject
            $this->apiLinker->deleteData("subject/".$subject["id"]);
        }
    }

}