<?php

namespace Phin\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;
use GuzzleHttp\Client;

class ThemeCommand extends Command
{
    private $files;
    private $client;

    public function __construct()
    {
        $this->files = new Filesystem;
        $this->client = new Client();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('theme')
            ->setDescription('Install a Bulmaswatch theme.')
            ->addArgument(
                'theme',
                InputArgument::REQUIRED,
                'Which theme'
            );
    }

    protected function fire()
    {
        $name = strtolower($this->input->getArgument('theme'));
        if (!$this->is_valid_site()) {
            $this->error("Cannot add theme to Phin, not initialized as Phin site.");
            return;
        }

        $res = $this->client->get('https://jenil.github.io/bulmaswatch/api/themes.json');
        if ($res->getStatusCode() != 200) {
            $this->error("Can't reach github.io, exiting.");
            return;
        }

        $json = json_decode($res->getBody());
        foreach ($json->themes as $theme) {
            if (strtolower($theme->name) == $name) {
                return $this->install($theme);
            }
        }
        $this->error("{$name} is not a valid Bulmaswatch theme.");
    }

    protected function install($theme)
    {
        // Copy the Phin project into the new site directory
        if (!file_exists($this->base . '/resources/assets/sass/')) {
            $this->files->makeDirectory($this->base . '/resources/assets/sass/');            
        }
        $this->client->request('GET',
                        $theme->scssVariables,
                        ['sink' => $this->base . '/resources/assets/sass/theme/_variables.scss']);
        $this->client->request('GET',
                        // Overrides not exposed in bulmaswatch API.
                        str_replace('_variables.scss', '_overrides.scss', $theme->scssVariables),
                        ['sink' => $this->base . '/resources/assets/sass/theme/_overrides.scss']);
        $this->info("{$theme->name} installed successfully!");
    }
}