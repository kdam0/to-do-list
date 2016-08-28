<?php
namespace App\Page;

class Index extends \Gt\Page\Logic {

    public function go() {
        // TODO 1: Make $taskList read from a persistent data source.
        // (for now, just use an array).

        $cookie_name = "todo_list_cookie";
        $cookie_value = $_SERVER["REMOTE_ADDR"];
        setcookie($cookie_name, $cookie_value, time()+(86400*7));

        $data = new \Gt\Data\Source\Csv("todo-app");
        $taskList = $data->getTable("taskList");

        // Handle user input first:
        if(isset($_POST["action"])) {
            switch($_POST["action"]) {
                case "add":
                    // TODO 2: Implement add action.
                    $taskList->add([
                        "done" => false,
                        "title" => $_POST["title"],
                        "user_addr" => $cookie_value,
                        "dateTimeCreated" => date("Y-m-d H:i:s"),
                    ]);
                    break;

                case "delete":
                    // TODO 3: Implement delete action.
                    $taskList->deleteRow($_POST["index"]);
                    break;

                case "update":
                    // TODO 4: Implement update action.
                    $taskList->updateRow($_POST["index"], [
                        "title" => $_POST["title"],
                    ]);
                    break;

                case "check":
                    // TODO 5: Implement check action.
                    // get the task
                    $currentTask = $taskList->get($_POST["index"]);

                    //toggle the done
                    $currentTask["done"] = !$currentTask["done"];

                    //update the row
                    $taskList->updateRow($_POST["index"], $currentTask);
                    break;
            }
        }

        if (isset($_COOKIE["todo_list_cookie"])) {
            echo "Welcome back! <br>";
        } else {
            echo "Hello new user!";
        }

        // Output task list:
        foreach ($taskList as $i => $task) {
            // Obtain a clone of the original <li>.

            //$currentTask = $taskList->get($_POST["index"]);
            //var_dump($task["user_addr"]);
            if ($task["user_addr"] == $_COOKIE["todo_list_cookie"]) {
                $li = $this->template->get("task");

                // Output the task details:
                $li->querySelector(".index")->value = $i;
                $li->querySelector("input[name='title']")->value = $task["title"];

                // If the task is done, mark it as done on the page.
                if($task["done"]) {
                    $li->classList->add("done");
                    $li->querySelector("[value='check']")->textContent = "Uncheck";
                }

                // Add the template back to the page.
                $li->insertTemplate();
            }


        }

        // TODO 7: Output one extra empty task, for adding new ones.
        // Output 'add' row:
        $li = $this->template->get("task");
        $li->querySelector("button[value='update']")->value = "add";
        $li->querySelector("input[name='title']")->setAttribute("autofocus", true);
        $li->insertTemplate();
    }

}