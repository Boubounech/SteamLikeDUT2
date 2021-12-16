<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *     itemOperations={"get", "put"},
 *     normalizationContext={"groups"={"game:read"}},
 *     denormalizationContext={"groups"={"game:write"}},
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "title": "partial",
 *     "description": "partial",
 *     "owner": "exact",
 *     "owner.username": "partial",
 * })
 *
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */

class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The title of the game you're looking at
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"game:read", "game:write", "user:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"game:read", "game:write"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"game:read", "game:write"})
     */
    private $link;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"game:read", "user:read"})
     */
    private $dlNumber;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"game:read", "game:write"})
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="game", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private $downloaders;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @ORM\JoinTable(name="user_likes")
     */
    private $Likers;

    public function __construct()
    {
        $this->dlNumber = 0;
        $this->comments = new ArrayCollection();
        $this->downloaders = new ArrayCollection();
        $this->Likers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getDlNumber(): ?int
    {
        return $this->dlNumber;
    }

    public function setDlNumber(int $dlNumber): self
    {
        $this->dlNumber = $dlNumber;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setGame($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGame() === $this) {
                $comment->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getDownloaders(): Collection
    {
        return $this->downloaders;
    }

    public function addDownloader(User $downloader): self
    {
        if (!$this->downloaders->contains($downloader)) {
            $this->downloaders[] = $downloader;
        }

        return $this;
    }

    public function removeDownloader(User $downloader): self
    {
        $this->downloaders->removeElement($downloader);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikers(): Collection
    {
        return $this->Likers;
    }

    public function addLiker(User $liker): self
    {
        if (!$this->Likers->contains($liker)) {
            $this->Likers[] = $liker;
        }

        return $this;
    }

    public function removeLiker(User $liker): self
    {
        $this->Likers->removeElement($liker);

        return $this;
    }
}
