<?php
class Moretools
{

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function generateUniqueID()
    {

        do {
            $hex_16_digit = "";
            $hex_string = "0123456789ABCDEF";
            for ($i = 0; $i < 16; $i++) {
                $hex_16_digit .= $hex_string[rand(0, strlen($hex_string) - 1)];
            }

            $query = "SELECT COUNT(*) as count FROM tbl_menu_more_tools WHERE pk_new_menu_key='" . $hex_16_digit . "'";
            $result = $this->pdo->query($query);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $rowCount = $row['count'];

        } while ($rowCount > 0);

        return $hex_16_digit;
    }


    public function generateUniqueBuketID()
    {

        do {
            $hex_16_digit = "";
            $hex_string = "0123456789ABCDEF";
            for ($i = 0; $i < 16; $i++) {
                $hex_16_digit .= $hex_string[rand(0, strlen($hex_string) - 1)];
            }

            $query = "SELECT COUNT(*) as count FROM tbl_menu_more_bucket WHERE pk_mmbucket_key ='" . $hex_16_digit . "'";
            $result = $this->pdo->query($query);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $rowCount = $row['count'];

        } while ($rowCount > 0);

        return $hex_16_digit;
    }
    public function generateUniqueBuketFileID()
    {

        do {
            $hex_16_digit = "";
            $hex_string = "0123456789ABCDEF";
            for ($i = 0; $i < 16; $i++) {
                $hex_16_digit .= $hex_string[rand(0, strlen($hex_string) - 1)];
            }

            $query = "SELECT COUNT(*) as count FROM  tbl_menu_more_bucket_files  WHERE pk_mmbucket_file_key ='" . $hex_16_digit . "'";
            $result = $this->pdo->query($query);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $rowCount = $row['count'];

        } while ($rowCount > 0);

        return $hex_16_digit;
    }

    // public function createNewMenu($data) {
    //     $pk_new_menu_key = $this->generateUniqueID();

    //     $query1 = "INSERT INTO tbl_menu_more_tools (pk_new_menu_key, menu_title, menu_descrption, menu_link, thumbnail_image,tool_visibility, fk_publisher_key, created_by, status) 
    //                VALUES (:pk_new_menu_key, :menu_title, :menu_descrption, :menu_link, :fk_publisher_key, :created_by, 1)";
    //     $stmt1 = $this->pdo->prepare($query1);
    //     $stmt1->bindParam(':pk_new_menu_key', $pk_new_menu_key);
    //     $stmt1->bindParam(':menu_title', $data['menu_title']);
    //     $stmt1->bindParam(':menu_descrption', $data['menu_descrption']);
    //     $stmt1->bindParam(':menu_link', $data['menu_link']);
    //     $stmt1->bindParam(':tool_visibility', $data['tool_category']);
    //     $stmt1->bindParam(':fk_publisher_key', $data['fk_publisher_key']);
    //     $stmt1->bindParam(':created_by', $data['created_by']);

    //     $stmt1->execute();
    // }
    public function createNewMenu($data)
    {
        $pk_new_menu_key = $this->generateUniqueID();
        if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['thumbnail_image']['tmp_name'];
            $fileName = $_FILES['thumbnail_image']['name'];
            $uploadFileDir = 'uploads/tool_thumbnail/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $destPath = $uploadFileDir . $fileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $data['thumbnail_image'] = $destPath;
            } else {
                throw new Exception('There was an error moving the uploaded file.');
            }
        } else {
            $data['thumbnail_image'] = NULL;
        }
        $query1 = "INSERT INTO tbl_menu_more_tools (pk_new_menu_key, menu_title, menu_descrption, menu_link, thumbnail_image, tool_visibility, fk_publisher_key, created_by, status) 
                   VALUES (:pk_new_menu_key, :menu_title, :menu_descrption, :menu_link, :thumbnail_image, :tool_visibility, :fk_publisher_key, :created_by, 1)";

        $stmt1 = $this->pdo->prepare($query1);
        $stmt1->bindParam(':pk_new_menu_key', $pk_new_menu_key);
        $stmt1->bindParam(':menu_title', $data['menu_title']);
        $stmt1->bindParam(':menu_descrption', $data['menu_descrption']);
        $stmt1->bindParam(':menu_link', $data['menu_link']);
        $stmt1->bindParam(':thumbnail_image', $data['thumbnail_image']); // Correctly use the data from above
        $stmt1->bindParam(':tool_visibility', $data['tool_category']); // Assuming this is the category
        $stmt1->bindParam(':fk_publisher_key', $data['fk_publisher_key']);
        $stmt1->bindParam(':created_by', $data['created_by']);

        // Execute the query
        $stmt1->execute();
    }


    //bkp -710
    // public function updateNewMenu($data) {
    //     $query = "UPDATE tbl_menu_more_tools 
    //               SET menu_title = :menu_title, 
    //                   menu_descrption = :menu_descrption, 
    //                   menu_link = :menu_link, 
    //                   fk_publisher_key = :fk_publisher_key, 
    //                   created_by = :created_by                      
    //               WHERE pk_new_menu_key = :pk_new_menu_key";

    //     $stmt = $this->pdo->prepare($query);

    //     // Bind parameters
    //     $stmt->bindParam(':menu_title', $data['menu_title']);
    //     $stmt->bindParam(':menu_descrption', $data['menu_descrption']);
    //     $stmt->bindParam(':menu_link', $data['menu_link']);
    //     $stmt->bindParam(':fk_publisher_key', $data['fk_publisher_key']);
    //     $stmt->bindParam(':created_by', $data['created_by']); 
    //     $stmt->bindParam(':pk_new_menu_key', $data['pk_new_menu_key']); 
    //     $stmt->execute();
    // }


    public function updateNewMenu($data)
    {
        // Initialize the new thumbnail image variable
        $newThumbnailImage = null;

        // Check if a new image is uploaded
        if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['thumbnail_image']['tmp_name'];
            $fileName = $_FILES['thumbnail_image']['name'];
            $uploadFileDir = 'uploads/tool_thumbnail/';
            $destPath = $uploadFileDir . $fileName;

            // Create directory if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            // Move the uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $newThumbnailImage = $destPath; // Set the new image path
            } else {
                throw new Exception('There was an error moving the uploaded file.');
            }
        }

        // Prepare the SQL update query
        $query = "UPDATE tbl_menu_more_tools 
                  SET menu_title = :menu_title, 
                      menu_descrption = :menu_descrption, 
                    --   menu_link = :menu_link, 
                      tool_visibility = :tool_visibility, 
                      fk_publisher_key = :fk_publisher_key, 
                      created_by = :created_by";

        // If a new thumbnail image was uploaded, include it in the query
        if ($newThumbnailImage) {
            $query .= ", thumbnail_image = :thumbnail_image";
        }

        $query .= " WHERE pk_new_menu_key = :pk_new_menu_key";

        // Prepare the statement
        $stmt = $this->pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':menu_title', $data['menu_title']);
        $stmt->bindParam(':menu_descrption', $data['menu_descrption']);
        // $stmt->bindParam(':menu_link', $data['menu_link']);
        $stmt->bindParam(':tool_visibility', $data['tool_visibility']);
        $stmt->bindParam(':fk_publisher_key', $data['fk_publisher_key']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':pk_new_menu_key', $data['pk_new_menu_key']);

        // Bind the new thumbnail image if it exists
        if ($newThumbnailImage) {
            $stmt->bindParam(':thumbnail_image', $newThumbnailImage);
        }

        // Execute the query
        $stmt->execute();
    }

    public function deleteNewMenu($data)
    {
        $status = 0;
        $query = "UPDATE tbl_menu_more_tools 
                  SET status = :status                          
                  WHERE pk_new_menu_key = :pk_new_menu_key";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':pk_new_menu_key', $data['pk_new_menu_key']);
        $stmt->execute();
    }


    public function NewmenuToolsList($data)
    {
        try {
            $query1 = "SELECT * FROM `tbl_menu_more_tools` WHERE created_by = :created_by AND status=1";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->bindParam(':created_by', $data['ukey']);
            $stmt1->execute();
            return $stmt1->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }
    }
    public function NewmenuToolsListAdmin($data)
    {
        try {
            $query1 = "SELECT DISTINCT tml.* FROM tbl_menu_more_tools tml
                       INNER JOIN tbl_access_control_tools tac ON tac.fk_menu_more_key = tml.pk_new_menu_key
                         WHERE tac.fk_school_key  = :fk_school_key AND tac.status=1 AND tml.status = 1";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->bindParam(':fk_school_key', $data['ukey']);
            $stmt1->execute();
            return $stmt1->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }
    }


    public function NewmenuToolsListStaff($data)
    {
        try {
            $query1 = "SELECT DISTINCT tml.* FROM tbl_menu_more_tools tml
            INNER JOIN tbl_access_control_tools tac ON tac.fk_menu_more_key = tml.pk_new_menu_key
              WHERE tac.fk_school_key  = :fk_school_key AND tac.status=1 AND tml.status = 1 AND tml.tool_visibility IN (2, 3)";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->bindParam(':fk_school_key', $data['school_key']);
            $stmt1->execute();
            return $stmt1->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }
    }
    public function NewmenuToolsListStudent($data)
    {
        try {
            $query1 = "SELECT DISTINCT tml.* FROM tbl_menu_more_tools tml
            INNER JOIN tbl_access_control_tools tac ON tac.fk_menu_more_key = tml.pk_new_menu_key
              WHERE tac.fk_school_key  = :fk_school_key AND tac.status=1 AND tml.status = 1 AND tml.tool_visibility = 3";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->bindParam(':fk_school_key', $data['school_key']);
            $stmt1->execute();
            return $stmt1->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array("success" => false, "message" => $e->getMessage());
        }
    }

    public function createMenumoreBucket($data)
    {
        try {
            // var_dump($data);
            // exit;
            $this->pdo->beginTransaction();
            $pk_mmbucket_key = $this->generateUniqueBuketID();
            $status = 1;
            $sql = "INSERT INTO tbl_menu_more_bucket (pk_mmbucket_key,fk_new_menu_key, mmbucket_name,mmbucket_description,fk_publisher_key,created_by, status) VALUES (:pk_mmbucket_key, :fk_new_menu_key, :mmbucket_name, :mmbucket_description,:fk_publisher_key, :created_by, :status)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":pk_mmbucket_key", $pk_mmbucket_key);
            $stmt->bindParam(":fk_new_menu_key", $data['pk_new_menu_key']);
            $stmt->bindParam(":mmbucket_name", $data['bucket_name']);
            $stmt->bindParam(":mmbucket_description", $data['bucket_description']);
            $stmt->bindParam(":fk_publisher_key", $data['ukey']);
            $stmt->bindParam(":created_by", $data['ukey']);
            $stmt->bindParam(":status", $status);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->pdo->commit();
                return array("success" => true, "message" => "New Bucket Created Successfully");
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return array("success" => false, "message" => $e->getMessage());
        }
    }

    public function updateMenumoreBucket($data)
    {
        try {
            $this->pdo->beginTransaction();
            $sql = "UPDATE tbl_menu_more_bucket 
                SET mmbucket_name = :mmbucket_name 
                WHERE pk_mmbucket_key = :pk_mmbucket_key";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":pk_mmbucket_key", $data['pk_mmbucket_key']);
            $stmt->execute();
            $this->pdo->commit();
            return array("success" => true, "message" => "Bucket name updated successfully");
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return array("success" => false, "message" => "Error: " . $e->getMessage());
        }
    }

    // public function PublisherBucketData($data) {
    //     try {
    //         $sql = "SELECT * FROM tbl_menu_more_bucket tmmb 
    //                 LEFT JOIN tbl_menu_more_bucket_files tmmbf 
    //                 ON tmmb.pk_mmbucket_key = tmmbf.fk_mmbucket_key 
    //                 AND tmmbf.status = 1
    //                 WHERE tmmb.fk_new_menu_key = :fk_new_menu_key 
    //                 AND tmmb.status = 1";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->bindValue(":fk_new_menu_key", $data['fk_new_menu_key'], PDO::PARAM_INT); 
    //         $stmt->execute();

    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array

    //         return array("success" => true, "data" => $result);
    //     } catch (Exception $e) {
    //         return array("success" => false, "message" => "Error: " . $e->getMessage());
    //     }
    // }





    public function PublisherBucketData($data)
    {
        try {
            $sql = "SELECT 
                    tmmb.pk_mmbucket_key, 
                    tmmb.mmbucket_name, 
                    tmmbf.pk_mmbucket_file_key, 
                    tmmbf.mmbucket_file_type, 
                    tmmbf.mmbucket_file_name, 
                    tmmbf.mmbucket_file_path 
                FROM tbl_menu_more_bucket tmmb 
                LEFT JOIN tbl_menu_more_bucket_files tmmbf 
                ON tmmb.pk_mmbucket_key = tmmbf.fk_mmbucket_key 
                AND tmmbf.status = 1
                WHERE tmmb.fk_new_menu_key = :fk_new_menu_key 
                AND tmmb.status = 1
                ORDER BY tmmb.pk_mmbucket_key, tmmbf.pk_mmbucket_file_key;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":fk_new_menu_key", $data['fk_new_menu_key']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $groupedData = [];
            foreach ($results as $row) {
                $bucketKey = $row['pk_mmbucket_key'];

                if (!isset($groupedData[$bucketKey])) {
                    $groupedData[$bucketKey] = [
                        "pk_mmbucket_key" => $bucketKey,
                        "bucket_name" => $row['mmbucket_name'],
                        "files" => []
                    ];
                }
                if (!empty($row['pk_mmbucket_file_key'])) {
                    $groupedData[$bucketKey]['files'][] = [
                        "pk_mmbucket_file_key" => $row['pk_mmbucket_file_key'],
                        "mmbucket_file_type" => $row['mmbucket_file_type'],
                        "mmbucket_file_name" => $row['mmbucket_file_name'],
                        "mmbucket_file_path" => $row['mmbucket_file_path']
                    ];
                }
            }

            return ["success" => true, "data" => array_values($groupedData)];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }


    public function createMenumoreBucketFiles($data)
    {
        try {
            $this->pdo->beginTransaction();
            $uploadDir = 'uploads/resourcebucket/';
            // $uploadDirToSAVE = 'uploads/resourcefiles/';

            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);
            $fileTypes = ['pdf' => 1, 'doc' => 2, 'docx' => 2, 'ppt' => 3, 'pptx' => 3, 'mp4' => 4, 'avi' => 4, 'mov' => 4];
            if (!empty($_FILES['file'])) {
                foreach ($_FILES['file']['name'] as $index => $fileName) {
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

                    $pk_mmbucket_file_key = $this->generateUniqueBuketFileID();
                    $tmpName = $_FILES['file']['tmp_name'][$index];
                    $uniqueFileName = time() . '_' . $fileName;
                    $filePath = $uploadDir . $uniqueFileName;
                    if (move_uploaded_file($tmpName, $filePath)) {
                        $sql = "INSERT INTO tbl_menu_more_bucket_files 
                            (pk_mmbucket_file_key, fk_mmbucket_key, mmbucket_file_type, mmbucket_file_name, mmbucket_file_path, status, created_on, created_by) 
                            VALUES (:pk_mmbucket_file_key, :fk_mmbucket_key, :type, :name, :path, 1, NOW(), :created_by)";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            ':pk_mmbucket_file_key' => $pk_mmbucket_file_key,
                            ':fk_mmbucket_key' => $data['fk_mmbucket_key'],
                            ':type' => $fileTypes[$fileExt],
                            ':name' => $fileNameWithoutExt,
                            ':path' => $filePath,
                            ':created_by' => $data['created_by']
                        ]);
                    }
                }
            }
            if (!empty($data['links'])) {
                foreach ($data['links'] as $link) {
                    if (!isset($link['url']) || !isset($link['name']))
                        continue;
                    $pk_mmbucket_file_key = $this->generateUniqueBuketFileID();
                    $sql = "INSERT INTO tbl_menu_more_bucket_files 
                        (pk_mmbucket_file_key, fk_mmbucket_key, mmbucket_file_type, mmbucket_file_name, mmbucket_file_path, status, created_on, created_by) 
                        VALUES (:pk_mmbucket_file_key, :fk_mmbucket_key, 5, :name, :path, 1, NOW(), :created_by)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        ':pk_mmbucket_file_key' => $pk_mmbucket_file_key,
                        ':fk_mmbucket_key' => $data['fk_mmbucket_key'],
                        ':name' => $link['name'],
                        ':path' => $link['url'],
                        ':created_by' => $data['created_by']
                    ]);
                }
            }
            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Files and links uploaded successfully'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



     public function MenumoreBuketFilesAdmin($data)
    {
        try {
            $sql = "SELECT 
                    tmmb.pk_mmbucket_key, 
                    tmmb.mmbucket_name, 
                    tmmbf.pk_mmbucket_file_key, 
                    tmmbf.mmbucket_file_type, 
                    tmmbf.mmbucket_file_name, 
                    tmmbf.mmbucket_file_path 
                FROM tbl_menu_more_bucket tmmb 
                LEFT JOIN tbl_menu_more_bucket_files tmmbf 
                ON tmmb.pk_mmbucket_key = tmmbf.fk_mmbucket_key 
                AND tmmbf.status = 1
                WHERE tmmb.fk_new_menu_key = :fk_new_menu_key 
                AND tmmb.status = 1
                ORDER BY tmmb.pk_mmbucket_key, tmmbf.pk_mmbucket_file_key;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":fk_new_menu_key", $data['fk_new_menu_key']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $groupedData = [];
            foreach ($results as $row) {
                $bucketKey = $row['pk_mmbucket_key'];

                if (!isset($groupedData[$bucketKey])) {
                    $groupedData[$bucketKey] = [
                        "pk_mmbucket_key" => $bucketKey,
                        "bucket_name" => $row['mmbucket_name'],
                        "files" => []
                    ];
                }
                if (!empty($row['pk_mmbucket_file_key'])) {
                    $groupedData[$bucketKey]['files'][] = [
                        "pk_mmbucket_file_key" => $row['pk_mmbucket_file_key'],
                        "mmbucket_file_type" => $row['mmbucket_file_type'],
                        "mmbucket_file_name" => $row['mmbucket_file_name'],
                        "mmbucket_file_path" => $row['mmbucket_file_path']
                    ];
                }
            }

            return ["success" => true, "data" => array_values($groupedData)];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }


     public function MenumoreBuketFilesStaff($data)
    {
        try {
            $sql = "SELECT 
                    tmmb.pk_mmbucket_key, 
                    tmmb.mmbucket_name, 
                    tmmbf.pk_mmbucket_file_key, 
                    tmmbf.mmbucket_file_type, 
                    tmmbf.mmbucket_file_name, 
                    tmmbf.mmbucket_file_path 
                FROM tbl_menu_more_bucket tmmb 
                LEFT JOIN tbl_menu_more_bucket_files tmmbf 
                ON tmmb.pk_mmbucket_key = tmmbf.fk_mmbucket_key 
                AND tmmbf.status = 1
                WHERE tmmb.fk_new_menu_key = :fk_new_menu_key 
                AND tmmb.status = 1
                ORDER BY tmmb.pk_mmbucket_key, tmmbf.pk_mmbucket_file_key;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":fk_new_menu_key", $data['fk_new_menu_key']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $groupedData = [];
            foreach ($results as $row) {
                $bucketKey = $row['pk_mmbucket_key'];

                if (!isset($groupedData[$bucketKey])) {
                    $groupedData[$bucketKey] = [
                        "pk_mmbucket_key" => $bucketKey,
                        "bucket_name" => $row['mmbucket_name'],
                        "files" => []
                    ];
                }
                if (!empty($row['pk_mmbucket_file_key'])) {
                    $groupedData[$bucketKey]['files'][] = [
                        "pk_mmbucket_file_key" => $row['pk_mmbucket_file_key'],
                        "mmbucket_file_type" => $row['mmbucket_file_type'],
                        "mmbucket_file_name" => $row['mmbucket_file_name'],
                        "mmbucket_file_path" => $row['mmbucket_file_path']
                    ];
                }
            }

            return ["success" => true, "data" => array_values($groupedData)];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }

     public function MenumoreBuketFilesStudent($data)
    {
        try {
            $sql = "SELECT 
                    tmmb.pk_mmbucket_key, 
                    tmmb.mmbucket_name, 
                    tmmbf.pk_mmbucket_file_key, 
                    tmmbf.mmbucket_file_type, 
                    tmmbf.mmbucket_file_name, 
                    tmmbf.mmbucket_file_path 
                FROM tbl_menu_more_bucket tmmb 
                LEFT JOIN tbl_menu_more_bucket_files tmmbf 
                ON tmmb.pk_mmbucket_key = tmmbf.fk_mmbucket_key 
                AND tmmbf.status = 1
                WHERE tmmb.fk_new_menu_key = :fk_new_menu_key 
                AND tmmb.status = 1
                ORDER BY tmmb.pk_mmbucket_key, tmmbf.pk_mmbucket_file_key;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":fk_new_menu_key", $data['fk_new_menu_key']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $groupedData = [];
            foreach ($results as $row) {
                $bucketKey = $row['pk_mmbucket_key'];

                if (!isset($groupedData[$bucketKey])) {
                    $groupedData[$bucketKey] = [
                        "pk_mmbucket_key" => $bucketKey,
                        "bucket_name" => $row['mmbucket_name'],
                        "files" => []
                    ];
                }
                if (!empty($row['pk_mmbucket_file_key'])) {
                    $groupedData[$bucketKey]['files'][] = [
                        "pk_mmbucket_file_key" => $row['pk_mmbucket_file_key'],
                        "mmbucket_file_type" => $row['mmbucket_file_type'],
                        "mmbucket_file_name" => $row['mmbucket_file_name'],
                        "mmbucket_file_path" => $row['mmbucket_file_path']
                    ];
                }
            }

            return ["success" => true, "data" => array_values($groupedData)];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }



}