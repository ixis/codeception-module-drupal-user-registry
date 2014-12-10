<?php

/**
 * Define Robo commands for building and testing Drupal User Registry Codeception module.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    /**
     * @type string
     *   Location of directory containing source code.
     */
    const SRC_DIR = "src";

    /**
     * @type string
     *   Location of directory containing documentation files.
     */
    const DOCS_DIR = "docs";

    /**
     * @type string
     *   Location of directory containing Codeception tests.
     */
    const TESTS_DIR = "tests";

    /**
     * Generate PhpDocumentator files.
     */
    public function docsGenerate()
    {
        $this->taskExec(sprintf("phpdoc run -d %s -t %s", self::SRC_DIR, self::DOCS_DIR))
            ->run();
    }

    /**
     * Clean PhpDocumentor directory.
     */
    public function docsClean()
    {
        $this->taskCleanDir(self::DOCS_DIR)->run();
    }

    /**
     * Run (functional) tests.
     */
    public function testsRun()
    {
        // Still have to build the *Tester classes if we've just checked out.
        $this->taskExec("cd tests && vendor/bin/codecept build && cd -")->run();

        $this->taskCodecept(self::TESTS_DIR . DIRECTORY_SEPARATOR . "vendor/bin/codecept")
            ->option("config", self::TESTS_DIR  . DIRECTORY_SEPARATOR . "codeception.yml")
            ->suite("functional")
            ->run();
    }
}
