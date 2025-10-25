<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DeletableFile;
use App\Helper\AppHelper;
use App\Repository\DeletableFileRepository;
use App\Service\Core\ConfigService;
use App\Service\Core\FileHandlerService;
use DateTime;

final readonly class DeletableFilesService
{
    public function __construct(
        private DeletableFileRepository  $deletableFileRepository,
        private ConfigService            $configService,
        private FileHandlerService       $fileHandlerService,
    ) {
    }

    public function getById(int $id): ?DeletableFile
    {
        return $this->deletableFileRepository->find($id);
    }

    /**
     * @return DeletableFile[]
     */
    public function getAll(): array
    {
        return $this->deletableFileRepository->findBy([], ['createdAt' => 'ASC']);
    }

    public function add(string $location, string $filename): DeletableFile
    {
        $deletableFile = new DeletableFile();

        $deletableFile
            ->setDirectory($location)
            ->setFilename($filename);

        $this->deletableFileRepository->save($deletableFile, true);

        return $deletableFile;
    }

    /**
     * @return DeletableFile[]
     */
    public function getAllByCriteria(string $directory, DateTime $from, DateTime $to): array
    {
        return $this->deletableFileRepository->findAllByCriteria($directory, $from, $to);
    }

    /**
     * Tools: Delete files from storage permanently
     *
     * @see AppHelper::TICKET_ATTACHMENT
     */
    public function deleteAllByCriteria(string $directory, DateTime $from, DateTime $to): int
    {
        $files = $this->getAllByCriteria($directory, $from, $to);

        $i = 0;

        if (count($files) > 0) {

            $dir = $this->configService->getParameter($directory);

            foreach ($files as $file) {
                $filename = $file->getFilename();

                if ($this->fileHandlerService->isFileExistsInDir($dir, $filename)) {
                    $this->delete($file, true);
                    $this->fileHandlerService->unlinkFile($dir, $filename);
                    $i++;
                }
            }
        }

        return $i;
    }

    public function delete(DeletableFile $deletableFile, bool $flush): void
    {
        $this->deletableFileRepository->remove($deletableFile, $flush);
    }
}