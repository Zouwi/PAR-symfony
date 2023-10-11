<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'groups_relation')]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: GroupPost::class)]
    private Collection $groupPosts;

    public function __construct()
    {
        $this->groupPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, GroupPost>
     */
    public function getGroupPosts(): Collection
    {
        return $this->groupPosts;
    }

    public function addGroupPost(GroupPost $groupPost): static
    {
        if (!$this->groupPosts->contains($groupPost)) {
            $this->groupPosts->add($groupPost);
            $groupPost->setAuthor($this);
        }

        return $this;
    }

    public function removeGroupPost(GroupPost $groupPost): static
    {
        if ($this->groupPosts->removeElement($groupPost)) {
            // set the owning side to null (unless already changed)
            if ($groupPost->getAuthor() === $this) {
                $groupPost->setAuthor(null);
            }
        }

        return $this;
    }
}
